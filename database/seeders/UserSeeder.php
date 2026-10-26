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
                'name' => 'Salsa Callista',
                'email' => 'admin@electromart.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '08116800101',
                'address' => 'Jl. Surya Kencana No. 45, Jakarta Barat',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ade Afrina',
                'email' => 'staff@electromart.test',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'phone' => '08116800102',
                'address' => 'Jl. Kebon Jeruk No. 18, Jakarta Barat',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Customer Demo',
                'email' => 'customer@electromart.test',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'phone' => '08126900200',
                'address' => 'Jl. Sudirman No. 12, Jakarta Pusat',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rahmania',
                'email' => 'rahmania@customer.local',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'phone' => '08126900201',
                'address' => 'Jl. Melati Indah No. 10, Tangerang',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Alfi Syahri',
                'email' => 'alfisyahri@customer.local',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'phone' => '08126900202',
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
