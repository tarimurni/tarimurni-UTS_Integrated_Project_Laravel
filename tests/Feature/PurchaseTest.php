<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;
    protected Supplier $supplier;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->staff = User::where('role', 'staff')->first();
        $this->customer = User::where('role', 'customer')->first();

        $this->supplier = Supplier::first();
        $this->productA = Product::where('sku', 'ELC-LPT-001')->first();
        $this->productB = Product::where('sku', 'ELC-SMP-001')->first();
    }

    public function test_admin_and_staff_can_view_purchases_and_create_form(): void
    {
        $this->actingAs($this->admin)->get('/purchases')->assertStatus(200);
        $this->actingAs($this->admin)->get('/purchases/create')->assertStatus(200);

        $this->actingAs($this->staff)->get('/purchases')->assertStatus(200);
        $this->actingAs($this->staff)->get('/purchases/create')->assertStatus(200);
    }

    public function test_customer_cannot_access_purchases(): void
    {
        $this->actingAs($this->customer)->get('/purchases')->assertStatus(403);
        $this->actingAs($this->customer)->get('/purchases/create')->assertStatus(403);
    }

    public function test_store_purchase_increments_stock_atomically_and_calculates_totals(): void
    {
        $initialStockA = $this->productA->stock;
        $initialStockB = $this->productB->stock;

        $qtyA = 5;
        $costA = 15000000;
        $qtyB = 10;
        $costB = 18000000;
        $expectedTotal = ($qtyA * $costA) + ($qtyB * $costB);

        $response = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => $qtyA,
                    'unit_cost' => $costA,
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => $qtyB,
                    'unit_cost' => $costB,
                ],
            ],
        ]);

        $purchase = Purchase::latest()->first();
        $this->assertNotNull($purchase);
        $response->assertRedirect("/purchases/{$purchase->id}");

        // Verify Header
        $this->assertStringStartsWith('PO-', $purchase->purchase_number);
        $this->assertEquals('completed', $purchase->status);
        $this->assertEquals($expectedTotal, $purchase->total);
        $this->assertEquals($this->staff->id, $purchase->user_id);
        $this->assertEquals($this->supplier->id, $purchase->supplier_id);

        // Verify Items Count
        $this->assertCount(2, $purchase->items);

        // Verify Automatic Stock Increment
        $this->productA->refresh();
        $this->productB->refresh();
        $this->assertEquals($initialStockA + $qtyA, $this->productA->stock);
        $this->assertEquals($initialStockB + $qtyB, $this->productB->stock);
    }

    public function test_purchase_validation_fails_with_invalid_data(): void
    {
        // Empty items
        $response = $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [],
        ]);
        $response->assertSessionHasErrors(['items']);

        // Zero quantity
        $response = $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 0,
                    'unit_cost' => 100000,
                ],
            ],
        ]);
        $response->assertSessionHasErrors(['items.0.quantity']);
    }

    public function test_admin_can_cancel_completed_purchase_and_stock_is_decremented(): void
    {
        $initialStock = $this->productA->stock;
        $qty = 8;

        // 1. Create Purchase
        $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => $qty,
                    'unit_cost' => 12000000,
                ],
            ],
        ]);

        $this->productA->refresh();
        $this->assertEquals($initialStock + $qty, $this->productA->stock);

        $purchase = Purchase::latest()->first();

        // 2. Admin cancels purchase
        $cancelResponse = $this->actingAs($this->admin)->post("/purchases/{$purchase->id}/cancel");
        $cancelResponse->assertRedirect("/purchases/{$purchase->id}");

        $purchase->refresh();
        $this->assertEquals('cancelled', $purchase->status);

        // 3. Stock rolled back
        $this->productA->refresh();
        $this->assertEquals($initialStock, $this->productA->stock);
    }

    public function test_staff_cannot_cancel_purchase(): void
    {
        // 1. Create Purchase
        $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 2,
                    'unit_cost' => 1000000,
                ],
            ],
        ]);

        $purchase = Purchase::latest()->first();

        // 2. Staff tries to cancel -> 403 Forbidden
        $response = $this->actingAs($this->staff)->post("/purchases/{$purchase->id}/cancel");
        $response->assertStatus(403);
    }

    public function test_cannot_cancel_purchase_if_stock_is_insufficient(): void
    {
        // 1. Create Purchase of 10 items
        $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 10,
                    'unit_cost' => 1000000,
                ],
            ],
        ]);

        $purchase = Purchase::latest()->first();

        // 2. Artificially simulate stock being sold/reduced to only 2 left
        $this->productA->update(['stock' => 2]);

        // 3. Try to cancel 10 items when only 2 remain in stock
        $response = $this->actingAs($this->admin)->post("/purchases/{$purchase->id}/cancel");
        $response->assertSessionHas('error');

        $purchase->refresh();
        $this->assertEquals('completed', $purchase->status); // Remains completed
    }
}
