<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySalesSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('email', 'admin@electromart.test')->first();
        $this->staff = User::where('email', 'staff@electromart.test')->first();
        $this->customer = User::where('email', 'customer@electromart.test')->first();
    }

    /**
     * Skenario 1: Auth
     * Guest tidak dapat membuka /dashboard (redirect ke /login),
     * dan login dengan password salah ditolak.
     */
    public function test_scenario_1_auth_guest_cannot_access_dashboard_and_invalid_password_is_rejected(): void
    {
        // 1. Guest akses /dashboard diarahkan ke /login
        $guestResponse = $this->get('/dashboard');
        $guestResponse->assertRedirect('/login');

        // 2. Login dengan password salah ditolak
        $loginResponse = $this->post('/login', [
            'email' => 'admin@electromart.test',
            'password' => 'password_salah_12345',
        ]);

        $loginResponse->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Skenario 2: Product Validation
     * Pembuatan produk dengan SKU duplikat, harga negatif, atau harga jual < harga beli ditolak server.
     */
    public function test_scenario_2_product_validation_duplicate_sku_and_negative_prices_are_rejected(): void
    {
        $category = Category::first();

        // 1. SKU duplikat (menggunakan SKU seeder yang sudah ada: ELC-LPT-001)
        $duplicateSkuResponse = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $category->id,
            'sku' => 'ELC-LPT-001',
            'name' => 'Laptop Duplikat SKU',
            'brand' => 'Test Brand',
            'purchase_price' => 5000000,
            'selling_price' => 6000000,
            'stock' => 10,
            'minimum_stock' => 2,
        ]);
        $duplicateSkuResponse->assertSessionHasErrors('sku');

        // 2. Harga negatif
        $negativePriceResponse = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $category->id,
            'sku' => 'ELC-UNIQ-099',
            'name' => 'Laptop Harga Negatif',
            'purchase_price' => -500000,
            'selling_price' => -100000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);
        $negativePriceResponse->assertSessionHasErrors(['purchase_price', 'selling_price']);

        // 3. Harga jual lebih rendah dari harga beli (selling_price < purchase_price)
        $lowerSellingPriceResponse = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $category->id,
            'sku' => 'ELC-UNIQ-100',
            'name' => 'Laptop Jual Rugi',
            'purchase_price' => 10000000,
            'selling_price' => 8000000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);
        $lowerSellingPriceResponse->assertSessionHasErrors('selling_price');
    }

    /**
     * Skenario 3: Purchase Stock-In
     * Transaksi pembelian yang sukses menambah stok produk sesuai kuantitas item.
     */
    public function test_scenario_3_purchase_stock_in_increments_stock_accurately(): void
    {
        $supplier = Supplier::first();
        $product = Product::first();
        $initialStock = $product->stock;
        $restockQty = 15;

        $response = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'purchased_at' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $restockQty,
                    'unit_cost' => 1000000,
                ],
            ],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $supplier->id,
            'status' => 'completed',
        ]);

        // Verifikasi stok produk bertambah tepat 15
        $product->refresh();
        $this->assertEquals($initialStock + $restockQty, $product->stock);
    }

    /**
     * Skenario 4: Sale Stock-Out
     * Transaksi penjualan yang sukses (oleh Staff maupun Customer) mengurangi stok produk sesuai kuantitas.
     */
    public function test_scenario_4_sale_stock_out_by_staff_and_customer_decrements_stock_accurately(): void
    {
        $product = Product::where('stock', '>=', 10)->first();
        $initialStock = $product->stock;

        // 1. Penjualan oleh Staff Kasir (2 unit)
        $staffSaleResponse = $this->actingAs($this->staff)->post('/sales', [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);
        $staffSaleResponse->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock);

        // 2. Pemesanan Checkout Mandiri oleh Customer (1 unit)
        $customerCheckoutResponse = $this->actingAs($this->customer)->post('/catalog/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);
        $customerCheckoutResponse->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals($initialStock - 3, $product->stock);
    }

    /**
     * Skenario 5: Insufficient Stock
     * Penjualan dengan kuantitas melebihi stok tersedia ditolak dengan error validasi,
     * dan stok di database tidak berubah.
     */
    public function test_scenario_5_insufficient_stock_sale_is_rejected_and_stock_remains_unchanged(): void
    {
        $product = Product::where('stock', '>', 0)->first();
        $currentStock = $product->stock;
        $excessiveQty = $currentStock + 50;

        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $excessiveQty,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Pastikan stok tidak berubah sama sekali
        $product->refresh();
        $this->assertEquals($currentStock, $product->stock);
    }

    /**
     * Skenario 6: Atomicity (Rollback)
     * Jika dalam satu transaksi multi-item terdapat 1 item yang stoknya tidak cukup,
     * maka seluruh transaksi di-rollback (header tidak tersimpan dan stok item pertama tidak berkurang).
     */
    public function test_scenario_6_atomicity_multi_item_transaction_rolls_back_if_one_item_has_insufficient_stock(): void
    {
        $products = Product::where('stock', '>=', 5)->take(2)->get();
        $item1 = $products[0];
        $item2 = $products[1];

        $initialStockItem1 = $item1->stock;
        $initialStockItem2 = $item2->stock;
        $initialSalesCount = Sale::count();

        // Coba transaksi multi-item: item1 valid (qty 2), namun item2 melebihi stok (qty + 999)
        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $item1->id,
                    'quantity' => 2, // Seharusnya valid jika berdiri sendiri
                ],
                [
                    'product_id' => $item2->id,
                    'quantity' => $initialStockItem2 + 999, // Gagal karena melebihi stok
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Verifikasi ATOMICITY:
        // 1. Header Sale tidak boleh bertambah
        $this->assertEquals($initialSalesCount, Sale::count());

        // 2. Stok item 1 tidak boleh berkurang (harus rollback ke kondisi awal)
        $item1->refresh();
        $this->assertEquals($initialStockItem1, $item1->stock);

        // 3. Stok item 2 juga tetap utuh
        $item2->refresh();
        $this->assertEquals($initialStockItem2, $item2->stock);
    }

    /**
     * Skenario 7: Authorization
     * User dengan role staff dan customer mendapat HTTP 403 Forbidden
     * jika mencoba mengakses endpoint khusus admin (hapus produk, kelola kategori, kelola user).
     */
    public function test_scenario_7_authorization_staff_and_customer_forbidden_from_admin_only_endpoints(): void
    {
        $product = Product::first();
        $category = Category::first();
        $user = User::first();

        // 1. Staff mencoba hapus produk -> 403 Forbidden
        $staffDeleteProduct = $this->actingAs($this->staff)->delete("/products/{$product->id}");
        $staffDeleteProduct->assertStatus(403);

        // 2. Staff mencoba membuat kategori baru -> 403 Forbidden
        $staffCreateCategory = $this->actingAs($this->staff)->post('/categories', [
            'name' => 'Kategori Ilegal Staff',
            'description' => 'Tidak boleh diakses oleh staff',
        ]);
        $staffCreateCategory->assertStatus(403);

        // 3. Customer mencoba mengakses manajemen user -> 403 Forbidden
        $customerAccessUsers = $this->actingAs($this->customer)->get('/users');
        $customerAccessUsers->assertStatus(403);

        // 4. Customer mencoba menghapus kategori -> 403 Forbidden
        $customerDeleteCategory = $this->actingAs($this->customer)->delete("/categories/{$category->id}");
        $customerDeleteCategory->assertStatus(403);
    }

    /**
     * Skenario 8: Search & Filter
     * Pencarian SKU produk dan filter kategori mengembalikan hasil yang akurat.
     */
    public function test_scenario_8_search_sku_and_filter_category_returns_accurate_results(): void
    {
        // 1. Pencarian berdasarkan SKU spesifik
        $skuResponse = $this->actingAs($this->staff)->get('/products?search=ELC-LPT-001');
        $skuResponse->assertStatus(200);
        $skuResponse->assertSee('ASUS ZenBook 14 OLED');
        $skuResponse->assertDontSee('Apple MacBook Air M3'); // Produk lain tidak boleh muncul

        // 2. Filter berdasarkan Kategori
        $category = Category::where('name', 'Monitor & Display')->first();
        $categoryResponse = $this->actingAs($this->staff)->get("/products?category_id={$category->id}");
        $categoryResponse->assertStatus(200);

        // Pastikan produk monitor muncul
        $categoryResponse->assertSee('LG UltraGear 27GP850-B');
        // Pastikan produk dari kategori Laptop tidak muncul dalam hasil
        $categoryResponse->assertDontSee('ASUS ZenBook 14 OLED');
    }
}
