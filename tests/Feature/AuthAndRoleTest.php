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
        $response->assertSee('ElectroMart');
        $response->assertSee('Masuk');
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
            'password' => 'password123',
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

    public function test_login_portals_can_be_rendered_directly(): void
    {
        // 1. Portal Customer
        $customerPortal = $this->get('/login/customer');
        $customerPortal->assertStatus(200);
        $customerPortal->assertSee('Portal Belanja Pelanggan');
        $customerPortal->assertSee('Nama Lengkap Pelanggan');
        $customerPortal->assertSee('Mulai Belanja Sekarang');

        // 2. Portal Staff
        $staffPortal = $this->get('/login/staff');
        $staffPortal->assertStatus(200);
        $staffPortal->assertSee('Portal Staff Gudang & Kasir', false);
        $staffPortal->assertSee('Email Staff');
        $staffPortal->assertSee('Masuk ke Portal Staff & Kasir', false);

        // 3. Portal Admin
        $adminPortal = $this->get('/login/admin');
        $adminPortal->assertStatus(200);
        $adminPortal->assertSee('Portal Administrator / Owner');
        $adminPortal->assertSee('Email Administrator');
        $adminPortal->assertSee('Masuk ke Portal Administrator');
    }

    public function test_customer_can_login_without_password_and_is_redirected_to_catalog(): void
    {
        $response = $this->post('/login/customer', [
            'name' => 'Fajar Pelanggan Baru',
            'phone' => '081298765432',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/catalog');

        $user = User::where('name', 'Fajar Pelanggan Baru')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertEquals('081298765432', $user->phone);
        $this->assertStringContainsString('@customer.local', $user->email);
    }

    public function test_existing_customer_can_login_without_password(): void
    {
        $existing = User::where('role', 'customer')->first();

        $response = $this->post('/login/customer', [
            'name' => $existing->name,
        ]);

        $this->assertAuthenticatedAs($existing);
        $response->assertRedirect('/catalog');
    }

    public function test_customer_login_fails_if_name_is_empty(): void
    {
        $response = $this->post('/login/customer', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    public function test_staff_portal_authenticates_staff_and_rejects_non_staff(): void
    {
        // 1. Staff valid can login
        $staffResponse = $this->post('/login/staff', [
            'email' => 'staff@electromart.test',
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();
        $staffResponse->assertRedirect('/dashboard');

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 2. Admin cannot login through staff portal
        $adminResponse = $this->post('/login/staff', [
            'email' => 'admin@electromart.test',
            'password' => 'password123',
        ]);
        $this->assertGuest();
        $adminResponse->assertSessionHasErrors('email');

        // 3. Customer cannot login through staff portal
        $customerResponse = $this->post('/login/staff', [
            'email' => 'customer@electromart.test',
            'password' => 'password123',
        ]);
        $this->assertGuest();
        $customerResponse->assertSessionHasErrors('email');
    }

    public function test_admin_portal_authenticates_admin_and_rejects_non_admin(): void
    {
        // 1. Admin valid can login
        $adminResponse = $this->post('/login/admin', [
            'email' => 'admin@electromart.test',
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();
        $adminResponse->assertRedirect('/dashboard');

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 2. Staff cannot login through admin portal
        $staffResponse = $this->post('/login/admin', [
            'email' => 'staff@electromart.test',
            'password' => 'password123',
        ]);
        $this->assertGuest();
        $staffResponse->assertSessionHasErrors('email');

        // 3. Customer cannot login through admin portal
        $customerResponse = $this->post('/login/admin', [
            'email' => 'customer@electromart.test',
            'password' => 'password123',
        ]);
        $this->assertGuest();
        $customerResponse->assertSessionHasErrors('email');
    }
}
