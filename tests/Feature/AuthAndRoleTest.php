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
}
