<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Nusantara Jaya Elektronik',
                'email' => 'supplier@nusantaraelektronik.co.id',
                'phone' => '021-5550101',
                'address' => 'Jl. Mangga Dua Raya No. 12, Sawah Besar, Jakarta Pusat',
            ],
            [
                'name' => 'CV Mega Komputindo Distribusi',
                'email' => 'sales@megakomputindo.co.id',
                'phone' => '021-5550102',
                'address' => 'Komp. Ruko Harco Mas Blok B-5, Mangga Dua, Jakarta Pusat',
            ],
            [
                'name' => 'PT Global Gadget Sinergi',
                'email' => 'info@globalgadget.co.id',
                'phone' => '021-5550103',
                'address' => 'Jl. Artha Gading Niaga Blok D No. 8, Kelapa Gading, Jakarta Utara',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['email' => $supplier['email']],
                $supplier
            );
        }
    }
}
