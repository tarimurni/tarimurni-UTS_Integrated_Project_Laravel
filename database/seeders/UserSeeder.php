<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Budi Santoso - Admin',
                'email' => 'admin@electromart.test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jl. Surya Kencana No. 45, Jakarta Barat',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Siti Rahma - Staff Gudang & Kasir',
                'email' => 'staff@electromart.test',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone' => '081234567891',
                'address' => 'Jl. Kebon Jeruk No. 18, Jakarta Barat',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Andi Pratama - Customer',
                'email' => 'customer@electromart.test',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '081234567892',
                'address' => 'Jl. Melati Indah No. 10, Tangerang',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rina Wijaya - Customer',
                'email' => 'rina@electromart.test',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '081234567893',
                'address' => 'Jl. Boulevard Raya Blok A2, Serpong',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
