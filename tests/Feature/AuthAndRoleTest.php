<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke ElectroMart');
        $response->assertSee('Email');
        $response->assertSee('Kata Sandi');
        $response->assertSee('Ingat sesi saya');
        $response->assertSee('Masuk ke Backoffice');
        $response->assertSee('Kembali ke Katalog Toko');
    }

    public function test_portal_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/portal/login');
        $response->assertStatus(200);
        $response->assertSee('Portal Backoffice');
        $response->assertSee('Email');
        $response->assertSee('Kata Sandi');
        $response->assertSee('Ingat sesi saya');
        $response->assertSee('Masuk ke Backoffice');
        $response->assertSee('Kembali ke Katalog Toko');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Pelanggan Baru');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::where('email', 'admin@electromart.test')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'admin123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::where('email', 'admin@electromart.test')->first();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_new_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Baru',
            'email' => 'budibaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08999999999',
            'address' => 'Jl. Merdeka No. 1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'budibaru@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/test-area');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');
    }

    public function test_staff_cannot_access_admin_only_area(): void
    {
        $staff = User::where('role', 'staff')->first();

        $response = $this->actingAs($staff)->get('/admin/test-area');
        $response->assertStatus(403);
    }

    public function test_staff_can_access_staff_area(): void
    {
        $staff = User::where('role', 'staff')->first();

        $response = $this->actingAs($staff)->get('/staff/test-area');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');
    }

    public function test_customer_cannot_access_admin_or_staff_area(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get('/admin/test-area');
        $response->assertStatus(403);

        $response2 = $this->actingAs($customer)->get('/staff/test-area');
        $response2->assertStatus(403);
    }

    public function test_customer_can_access_customer_area(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get('/customer/test-area');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('role', 'admin')->first();

        $response = $this->actingAs($user)->post('/logout');
        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_public_catalog_can_be_accessed_without_login(): void
    {
        // 1. Root route gives direct catalog access
        $rootResponse = $this->get('/');
        $rootResponse->assertStatus(200);
        $rootResponse->assertSee('Katalog Resmi & Bergaransi', false);
        $rootResponse->assertSee('Login Pegawai');

        // 2. /catalog route gives direct catalog access
        $catalogResponse = $this->get('/catalog');
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('Katalog Resmi & Bergaransi', false);
        $catalogResponse->assertSee('Login Pegawai');
    }

    public function test_automatic_role_detection_on_login(): void
    {
        // 1. Admin login redirects to dashboard
        $adminResponse = $this->post('/login', [
            'email' => 'admin@electromart.test',
            'password' => 'admin123',
        ]);
        $this->assertAuthenticated();
        $adminResponse->assertRedirect('/dashboard');

        $this->post('/logout');

        // 2. Staff login redirects to dashboard
        $staffResponse = $this->post('/login', [
            'email' => 'staff@electromart.test',
            'password' => 'staff123',
        ]);
        $this->assertAuthenticated();
        $staffResponse->assertRedirect('/dashboard');

        $this->post('/logout');

        // 3. Customer login redirects to catalog
        $customerResponse = $this->post('/login', [
            'email' => 'customer@electromart.test',
            'password' => 'customer123',
        ]);
        $this->assertAuthenticated();
        $customerResponse->assertRedirect('/catalog');
    }

    public function test_staff_cannot_login_using_staff_password_to_admin_account(): void
    {
        $this->post('/login', [
            'email' => 'admin@electromart.test',
            'password' => 'staff123',
        ]);
        $this->assertGuest();
    }

    public function test_admin_cannot_login_using_admin_password_to_staff_account(): void
    {
        $this->post('/login', [
            'email' => 'staff@electromart.test',
            'password' => 'admin123',
        ]);
        $this->assertGuest();
    }

    public function test_login_screens_do_not_contain_quick_login_or_password_leaks(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertDontSee('Akses Cepat');
        $loginResponse->assertDontSee('1-Klik Masuk Cepat');
        $loginResponse->assertDontSee('Pass:');
        $loginResponse->assertDontSee('admin123');
        $loginResponse->assertDontSee('staff123');
        $loginResponse->assertDontSee('customer123');
        $loginResponse->assertDontSee('password123');

        $portalResponse = $this->get('/portal/login');
        $portalResponse->assertDontSee('Akses Cepat');
        $portalResponse->assertDontSee('1-Klik Masuk Cepat');
        $portalResponse->assertDontSee('Pass:');
        $portalResponse->assertDontSee('admin123');
        $portalResponse->assertDontSee('staff123');
        $portalResponse->assertDontSee('customer123');
        $portalResponse->assertDontSee('password123');
    }

    public function test_guest_can_checkout_from_catalog_passwordless(): void
    {
        $product = \App\Models\Product::first();

        $response = $this->post('/catalog/checkout', [
            'customer_name' => 'Budi Pelanggan Langsung',
            'customer_phone' => '081288887777',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->assertAuthenticated();
        $user = User::where('name', 'Budi Pelanggan Langsung')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertEquals('081288887777', $user->phone);

        $sale = \App\Models\Sale::where('customer_id', $user->id)->first();
        $this->assertNotNull($sale);
        $response->assertRedirect(route('sales.show', $sale));
    }

    public function test_guest_checkout_requires_customer_name(): void
    {
        $product = \App\Models\Product::first();

        $response = $this->post('/catalog/checkout', [
            'customer_name' => '',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('customer_name');
        $this->assertGuest();
    }
}
