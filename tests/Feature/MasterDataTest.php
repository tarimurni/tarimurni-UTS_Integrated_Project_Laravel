<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->staff = User::where('role', 'staff')->first();
        $this->customer = User::where('role', 'customer')->first();
    }

    /* -------------------------------------------------------------
     * CATEGORIES TESTS
     * ------------------------------------------------------------- */
    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Audio & Sound System',
            'description' => 'Speaker, soundbar, dan earphone bluetooth.',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Audio & Sound System']);
    }

    public function test_category_requires_unique_name(): void
    {
        $existing = Category::first();

        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => $existing->name,
            'description' => 'Test duplicate',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_cannot_delete_category_with_products(): void
    {
        $categoryWithProducts = Category::whereHas('products')->first();

        $response = $this->actingAs($this->admin)->delete("/categories/{$categoryWithProducts->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $categoryWithProducts->id]);
    }

    public function test_staff_can_view_categories_but_cannot_create_or_delete(): void
    {
        $category = Category::first();

        // Staff can view
        $this->actingAs($this->staff)->get('/categories')->assertStatus(200);
        $this->actingAs($this->staff)->get("/categories/{$category->id}")->assertStatus(200);

        // Staff cannot access create / delete
        $this->actingAs($this->staff)->get('/categories/create')->assertStatus(403);
        $this->actingAs($this->staff)->post('/categories', ['name' => 'Test'])->assertStatus(403);
        $this->actingAs($this->staff)->delete("/categories/{$category->id}")->assertStatus(403);
    }

    /* -------------------------------------------------------------
     * SUPPLIERS TESTS
     * ------------------------------------------------------------- */
    public function test_admin_can_create_supplier(): void
    {
        $response = $this->actingAs($this->admin)->post('/suppliers', [
            'name' => 'PT Sumber Makmur Elektronik',
            'phone' => '021-99887766',
            'email' => 'sales@sumbermakmur.test',
            'address' => 'Jakarta Barat',
        ]);

        $response->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', ['email' => 'sales@sumbermakmur.test']);
    }

    public function test_staff_cannot_create_supplier(): void
    {
        $response = $this->actingAs($this->staff)->post('/suppliers', [
            'name' => 'PT Ilegal Supplier',
            'phone' => '08123456789',
            'email' => 'ilegal@test.com',
        ]);

        $response->assertStatus(403);
    }

    /* -------------------------------------------------------------
     * PRODUCTS TESTS
     * ------------------------------------------------------------- */
    public function test_admin_can_create_product(): void
    {
        $category = Category::first();

        $response = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'ELC-TEST-001',
            'name' => 'Test Monitor Gaming 24 Inch',
            'category_id' => $category->id,
            'brand' => 'AOC',
            'purchase_price' => 1500000,
            'selling_price' => 1850000,
            'stock' => 10,
            'minimum_stock' => 3,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['sku' => 'ELC-TEST-001']);
    }

    public function test_product_selling_price_must_be_gte_purchase_price(): void
    {
        $category = Category::first();

        $response = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'ELC-TEST-ERR',
            'name' => 'Rugi Gaming Device',
            'category_id' => $category->id,
            'purchase_price' => 2000000,
            'selling_price' => 1500000, // Less than purchase price!
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $response->assertSessionHasErrors(['selling_price']);
    }

    public function test_product_soft_delete_preserves_record_in_database(): void
    {
        $product = Product::first();

        $response = $this->actingAs($this->admin)->delete("/products/{$product->id}");

        $response->assertRedirect('/products');

        // Check soft-deleted
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_staff_can_view_products_but_cannot_create_or_edit(): void
    {
        $product = Product::first();

        // View allowed
        $this->actingAs($this->staff)->get('/products')->assertStatus(200);
        $this->actingAs($this->staff)->get("/products/{$product->id}")->assertStatus(200);

        // Mutating blocked
        $this->actingAs($this->staff)->get('/products/create')->assertStatus(403);
        $this->actingAs($this->staff)->get("/products/{$product->id}/edit")->assertStatus(403);
        $this->actingAs($this->staff)->delete("/products/{$product->id}")->assertStatus(403);
    }

    /* -------------------------------------------------------------
     * USER MANAGEMENT TESTS
     * ------------------------------------------------------------- */
    public function test_admin_can_access_user_management_and_create_user(): void
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Staff Baru Gudang',
            'email' => 'staffbaru@electromart.test',
            'role' => 'staff',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123444444',
        ]);

        $createResponse->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'staffbaru@electromart.test', 'role' => 'staff']);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $this->actingAs($this->staff)->get('/users')->assertStatus(403);
        $this->actingAs($this->customer)->get('/users')->assertStatus(403);
    }
}
