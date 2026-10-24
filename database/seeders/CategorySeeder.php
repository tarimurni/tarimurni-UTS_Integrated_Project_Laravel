<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Televisi & Home Audio',
                'description' => 'Smart TV 4K/8K, OLED TV, Soundbar, Home Theater, dan sistem audio resmi berkualitas tinggi.',
            ],
            [
                'name' => 'Elektronik Rumah Tangga (Kulkas, AC, Mesin Cuci)',
                'description' => 'Kulkas Smart Inverter, mesin cuci otomatis, AC hemat energi, dan microwave oven untuk hunian modern.',
            ],
            [
                'name' => 'Laptop & Komputer',
                'description' => 'Laptop premium, ultrabook profesional, laptop gaming RTX, dan PC desktop bertenaga tinggi.',
            ],
            [
                'name' => 'Smartphone & Tablet',
                'description' => 'Smartphone flagship bergaransi resmi, tablet produktivitas, dan ekosistem gadget generasi terbaru.',
            ],
            [
                'name' => 'Kamera & Perangkat Pintar',
                'description' => 'Kamera mirrorless profesional, lensa fotografi, drone, dan smart home devices terkini.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
