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
                'name' => 'Laptop & Notebook',
                'description' => 'Perangkat laptop portable untuk kebutuhan kerja, perkuliahan, gaming, dan produktivitas profesional.',
            ],
            [
                'name' => 'Smartphone & Tablet',
                'description' => 'Smartphone andalan dan tablet cerdas generasi terbaru dengan performa tinggi.',
            ],
            [
                'name' => 'Komponen PC & Hardware',
                'description' => 'Processor, motherboard, RAM, VGA kartu grafis, storage SSD, dan PSU rakitan PC.',
            ],
            [
                'name' => 'Aksesoris & Periferal',
                'description' => 'Keyboard mechanical, mouse gaming nirkabel, headset audio, webcam HD, dan aksesori periferal.',
            ],
            [
                'name' => 'Monitor & Display',
                'description' => 'Monitor LED / IPS resolusi tinggi dengan refresh rate tinggi untuk gaming dan desain grafis.',
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
