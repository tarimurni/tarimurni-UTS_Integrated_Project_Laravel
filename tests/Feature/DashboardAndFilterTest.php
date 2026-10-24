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

class DashboardAndFilterTest extends TestCase
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
     * Test admin dashboard aggregates and widgets.
     */
    public function test_admin_and_staff_dashboard_displays_real_time_aggregates(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Produk Elektronik Aktif');
        $response->assertSee('Penjualan Hari Ini');
        $response->assertSee('Penjualan Bulan Ini');
        $response->assertSee('Pembelian Bulan Ini');
        $response->assertSee('Peringatan: Stok Barang Menipis');
        $response->assertSee('5 Transaksi Penjualan Terakhir');
    }

    /**
     * Test customer dashboard shows personalized statistics and catalog link.
     */
    public function test_customer_dashboard_displays_personal_statistics_and_catalog_button(): void
    {
        $response = $this->actingAs($this->customer)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Pesanan Anda');
        $response->assertSee('Akumulasi Belanja');
        $response->assertSee('Katalog Resmi ElectroMart');
        $response->assertSee('Pesanan Terakhir Anda');
        $response->assertSee(route('catalog.index'));
    }

    /**
     * Test product search by SKU and Name.
     */
    public function test_product_search_by_sku_and_name(): void
    {
        // Search by SKU
        $response = $this->actingAs($this->staff)->get('/products?search=ELC-LPT-001');
        $response->assertStatus(200);
        $response->assertSee('Apple MacBook Pro 14 M3 Pro');

        // Search by Name keyword
        $response2 = $this->actingAs($this->staff)->get('/products?search=Zephyrus');
        $response2->assertStatus(200);
        $response2->assertSee('Laptop Gaming ASUS ROG Zephyrus G16');
    }

    /**
     * Test product filter by category and stock status.
     */
    public function test_product_filter_by_category_and_stock_status(): void
    {
        $category = Category::where('name', 'Laptop & Komputer')->first();

        $response = $this->actingAs($this->staff)->get('/products?category_id=' . $category->id);
        $response->assertStatus(200);
        $response->assertSee('Apple MacBook Pro 14 M3 Pro');

        // Filter low stock
        $responseLowStock = $this->actingAs($this->staff)->get('/products?stock_status=low_stock');
        $responseLowStock->assertStatus(200);
        // Low stock products from seeder include ASUS ROG Zephyrus G16 (stock: 2, min: 5)
        $responseLowStock->assertSee('Laptop Gaming ASUS ROG Zephyrus G16');
    }

    /**
     * Test customer catalog search and stock filter.
     */
    public function test_catalog_search_and_stock_status_filter(): void
    {
        $response = $this->actingAs($this->customer)->get('/catalog?search=Bravia');
        $response->assertStatus(200);
        $response->assertSee('Sony Bravia OLED 65 Inch');

        // Filter available stock
        $responseAvailable = $this->actingAs($this->customer)->get('/catalog?stock_status=available');
        $responseAvailable->assertStatus(200);
    }

    /**
     * Test purchases search, date range filter, and status filter.
     */
    public function test_purchases_search_and_date_range_filter(): void
    {
        $supplier = Supplier::first();

        // Create test purchase
        $purchase = Purchase::create([
            'purchase_number' => 'PO-TEST-2026-0001',
            'supplier_id' => $supplier->id,
            'user_id' => $this->admin->id,
            'total' => 15000000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        // Search by purchase number
        $response = $this->actingAs($this->staff)->get('/purchases?search=PO-TEST-2026-0001');
        $response->assertStatus(200);
        $response->assertSee('PO-TEST-2026-0001');

        // Filter by date range
        $today = now()->format('Y-m-d');
        $responseDate = $this->actingAs($this->staff)->get("/purchases?start_date={$today}&end_date={$today}");
        $responseDate->assertStatus(200);
        $responseDate->assertSee('PO-TEST-2026-0001');

        // Filter by status completed
        $responseStatus = $this->actingAs($this->staff)->get('/purchases?status=completed');
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('PO-TEST-2026-0001');

        // Filter by status cancelled
        $responseCancelled = $this->actingAs($this->staff)->get('/purchases?status=cancelled');
        $responseCancelled->assertStatus(200);
        $responseCancelled->assertDontSee('PO-TEST-2026-0001');
    }

    /**
     * Test sales search, date range filter, and status filter.
     */
    public function test_sales_search_and_date_range_filter(): void
    {
        // Create test sale
        $sale = Sale::create([
            'sale_number' => 'INV-TEST-2026-0001',
            'user_id' => $this->admin->id,
            'customer_id' => $this->customer->id,
            'total' => 8500000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        // Search by sale number
        $response = $this->actingAs($this->staff)->get('/sales?search=INV-TEST-2026-0001');
        $response->assertStatus(200);
        $response->assertSee('INV-TEST-2026-0001');

        // Filter by date range
        $today = now()->format('Y-m-d');
        $responseDate = $this->actingAs($this->staff)->get("/sales?start_date={$today}&end_date={$today}");
        $responseDate->assertStatus(200);
        $responseDate->assertSee('INV-TEST-2026-0001');

        // Filter by status completed
        $responseStatus = $this->actingAs($this->staff)->get('/sales?status=completed');
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('INV-TEST-2026-0001');

        // Filter by status cancelled
        $responseCancelled = $this->actingAs($this->staff)->get('/sales?status=cancelled');
        $responseCancelled->assertStatus(200);
        $responseCancelled->assertDontSee('INV-TEST-2026-0001');
    }

    /**
     * Test pagination retains query string params.
     */
    public function test_pagination_retains_search_and_filter_query_params(): void
    {
        $category = Category::first();
        // Create 2 extra products so total > 10 items (page 1 and page 2 exist)
        Product::create([
            'category_id' => $category->id,
            'sku' => 'ELC-EXTRA-001',
            'name' => 'Extra Electronic Item 1',
            'purchase_price' => 1000000,
            'selling_price' => 1500000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);
        Product::create([
            'category_id' => $category->id,
            'sku' => 'ELC-EXTRA-002',
            'name' => 'Extra Electronic Item 2',
            'purchase_price' => 1000000,
            'selling_price' => 1500000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $response = $this->actingAs($this->staff)->get('/products?search=ELC&page=1');
        $response->assertStatus(200);

        // Assert links contain query parameters
        $this->assertStringContainsString('search=ELC', $response->getContent());
        $this->assertStringContainsString('page=2', $response->getContent());
    }
}
