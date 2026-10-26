<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer1;
    protected User $customer2;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->staff = User::where('role', 'staff')->first();
        $this->customer1 = User::where('role', 'customer')->first();
        $this->customer2 = User::where('role', 'customer')->skip(1)->first();

        $this->productA = Product::where('sku', 'ELC-LPT-001')->first();
        $this->productB = Product::where('sku', 'ELC-SMP-001')->first();
    }

    public function test_admin_and_staff_can_access_cashier_sales_index_and_create(): void
    {
        $this->actingAs($this->admin)->get('/sales')->assertStatus(200);
        $this->actingAs($this->admin)->get('/sales/create')->assertStatus(200);

        $this->actingAs($this->staff)->get('/sales')->assertStatus(200);
        $this->actingAs($this->staff)->get('/sales/create')->assertStatus(200);
    }

    public function test_customer_cannot_access_cashier_create(): void
    {
        $this->actingAs($this->customer1)->get('/sales/create')->assertStatus(403);
    }

    public function test_staff_can_store_sale_with_atomic_stock_decrement_and_server_price(): void
    {
        $initialStockA = $this->productA->stock;
        $qty = 2;
        $expectedTotal = $qty * $this->productA->selling_price;

        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_id' => $this->customer1->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => $qty,
                ],
            ],
        ]);

        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $response->assertRedirect("/sales/{$sale->id}");

        // Assert Header
        $this->assertStringStartsWith('INV-', $sale->sale_number);
        $this->assertEquals('completed', $sale->status);
        $this->assertEquals($expectedTotal, $sale->total);
        $this->assertEquals($this->staff->id, $sale->user_id);
        $this->assertEquals($this->customer1->id, $sale->customer_id);

        // Assert Stock Decremented
        $this->productA->refresh();
        $this->assertEquals($initialStockA - $qty, $this->productA->stock);
    }

    public function test_sale_fails_if_quantity_exceeds_available_stock(): void
    {
        $availableStock = $this->productA->stock;
        $excessiveQty = $availableStock + 10;

        $response = $this->actingAs($this->staff)->post('/sales', [
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => $excessiveQty,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['items']);

        // Assert stock remains untouched
        $this->productA->refresh();
        $this->assertEquals($availableStock, $this->productA->stock);
        $this->assertDatabaseMissing('sales', ['user_id' => $this->staff->id]);
    }

    public function test_customer_can_view_catalog_and_checkout_order(): void
    {
        // 1. Catalog access
        $this->actingAs($this->customer1)->get('/catalog')->assertStatus(200);

        $initialStock = $this->productB->stock;
        $qty = 1;

        // 2. Checkout
        $response = $this->actingAs($this->customer1)->post('/catalog/checkout', [
            'items' => [
                [
                    'product_id' => $this->productB->id,
                    'quantity' => $qty,
                ],
            ],
        ]);

        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $response->assertRedirect("/sales/{$sale->id}");

        $this->assertEquals($this->customer1->id, $sale->customer_id);
        $this->assertEquals($this->customer1->id, $sale->user_id);

        // Verify stock deducted
        $this->productB->refresh();
        $this->assertEquals($initialStock - $qty, $this->productB->stock);

        // 3. Customer can view their order history
        $this->actingAs($this->customer1)->get('/customer/orders')->assertStatus(200);
    }

    public function test_customer_cannot_view_another_customers_invoice(): void
    {
        // Sale for customer1
        $sale = Sale::create([
            'user_id' => $this->customer1->id,
            'customer_id' => $this->customer1->id,
            'sale_number' => 'INV-20261025-PRIV',
            'total' => 5000000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        // Customer 1 can view
        $this->actingAs($this->customer1)->get("/sales/{$sale->id}")->assertStatus(200);

        // Admin and Staff can view
        $this->actingAs($this->admin)->get("/sales/{$sale->id}")->assertStatus(200);
        $this->actingAs($this->staff)->get("/sales/{$sale->id}")->assertStatus(200);

        // Customer 2 tries to view Customer 1's invoice -> 403 Forbidden
        $this->actingAs($this->customer2)->get("/sales/{$sale->id}")->assertStatus(403);
    }

    public function test_admin_can_cancel_sale_and_stock_is_restored(): void
    {
        $initialStock = $this->productA->stock;
        $qty = 3;

        // Create Sale
        $this->actingAs($this->staff)->post('/sales', [
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => $qty,
                ],
            ],
        ]);

        $this->productA->refresh();
        $this->assertEquals($initialStock - $qty, $this->productA->stock);

        $sale = Sale::latest()->first();

        // Admin cancels sale
        $cancelResponse = $this->actingAs($this->admin)->post("/sales/{$sale->id}/cancel");
        $cancelResponse->assertRedirect("/sales/{$sale->id}");

        $sale->refresh();
        $this->assertEquals('cancelled', $sale->status);

        // Stock restored
        $this->productA->refresh();
        $this->assertEquals($initialStock, $this->productA->stock);
    }

    public function test_staff_cannot_cancel_sale(): void
    {
        $sale = Sale::create([
            'user_id' => $this->staff->id,
            'customer_id' => null,
            'sale_number' => 'INV-20261025-CANC',
            'total' => 1000000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $response = $this->actingAs($this->staff)->post("/sales/{$sale->id}/cancel");
        $response->assertStatus(403);
    }

    public function test_checkout_with_payment_method_saves_gateway_reference_and_status(): void
    {
        $response = $this->actingAs($this->customer1)->post('/catalog/checkout', [
            'payment_method' => 'va_bca',
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);
        $response->assertRedirect("/sales/{$sale->id}");
        $response->assertSessionHas('open_snap_modal', true);

        $this->assertEquals('va_bca', $sale->payment_method);
        $this->assertEquals('paid', $sale->payment_status);
        $this->assertStringStartsWith('PG-MID-', $sale->payment_reference);
        $this->assertNotNull($sale->paid_at);
        $this->assertStringContainsString('80777', $sale->virtual_account_number);
    }

    public function test_admin_cancelling_sale_sets_payment_status_to_refunded(): void
    {
        $sale = Sale::create([
            'user_id' => $this->staff->id,
            'customer_id' => $this->customer1->id,
            'sale_number' => 'INV-20261026-REFU',
            'total' => 2500000,
            'status' => 'completed',
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'payment_reference' => 'PG-MID-20261026-99999',
            'paid_at' => now(),
            'sold_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post("/sales/{$sale->id}/cancel");
        $response->assertRedirect("/sales/{$sale->id}");

        $sale->refresh();
        $this->assertEquals('cancelled', $sale->status);
        $this->assertEquals('refunded', $sale->payment_status);
    }

    public function test_customer_can_access_payment_gateway_simulator_and_simulate_success(): void
    {
        $sale = Sale::create([
            'user_id' => $this->customer1->id,
            'customer_id' => $this->customer1->id,
            'sale_number' => 'INV-20261026-SNAP',
            'total' => 1500000,
            'status' => 'completed',
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'payment_reference' => 'PG-MID-20261026-12345',
            'paid_at' => now(),
            'sold_at' => now(),
        ]);

        // Customer can open dedicated /sales/{sale}/payment page
        $this->actingAs($this->customer1)->get("/sales/{$sale->id}/payment")->assertStatus(200);

        // Simulation post callback
        $simResponse = $this->actingAs($this->customer1)->post("/sales/{$sale->id}/payment/simulate", [
            'payment_method' => 'va_bsi',
        ]);
        $simResponse->assertRedirect("/sales/{$sale->id}");

        $sale->refresh();
        $this->assertEquals('va_bsi', $sale->payment_method);
        $this->assertEquals('paid', $sale->payment_status);
        $this->assertNotNull($sale->paid_at);
    }
}
