<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');

        $products = [
            // 1. Televisi & Home Audio
            [
                'category' => 'Televisi & Home Audio',
                'sku' => 'ELC-TV-001',
                'name' => 'Samsung Smart TV 55 Inch Crystal UHD 4K',
                'brand' => 'Samsung',
                'image_url' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Titan Black',
                'color_hex' => '#1C1C1E',
                'purchase_price' => 6500000.00,
                'selling_price' => 7899000.00,
                'stock' => 8,
                'minimum_stock' => 3,
            ],
            [
                'category' => 'Televisi & Home Audio',
                'sku' => 'ELC-TV-002',
                'name' => 'Sony Bravia OLED 65 Inch Dolby Atmos',
                'brand' => 'Sony',
                'image_url' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Dark Silver',
                'color_hex' => '#4A4D50',
                'purchase_price' => 18000000.00,
                'selling_price' => 21999000.00,
                'stock' => 6,
                'minimum_stock' => 2,
            ],

            // 2. Elektronik Rumah Tangga (Kulkas, AC, Mesin Cuci)
            [
                'category' => 'Elektronik Rumah Tangga (Kulkas, AC, Mesin Cuci)',
                'sku' => 'ELC-RMT-001',
                'name' => 'Kulkas LG 2 Pintu Smart Inverter 395L',
                'brand' => 'LG',
                'image_url' => 'https://images.unsplash.com/photo-1584568694244-14fbdf83bd30?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Platinum Silver',
                'color_hex' => '#C0C5CE',
                'purchase_price' => 5200000.00,
                'selling_price' => 6299000.00,
                'stock' => 10,
                'minimum_stock' => 3,
            ],
            [
                'category' => 'Elektronik Rumah Tangga (Kulkas, AC, Mesin Cuci)',
                'sku' => 'ELC-RMT-002',
                'name' => 'Mesin Cuci Sharp Front Loading 8.5 Kg',
                'brand' => 'Sharp',
                'image_url' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Pearl White',
                'color_hex' => '#F4F6F8',
                'purchase_price' => 4300000.00,
                'selling_price' => 5199000.00,
                'stock' => 7,
                'minimum_stock' => 3,
            ],
            [
                'category' => 'Elektronik Rumah Tangga (Kulkas, AC, Mesin Cuci)',
                'sku' => 'ELC-RMT-003',
                'name' => 'Microwave Oven Panasonic Grill 25L',
                'brand' => 'Panasonic',
                'image_url' => 'https://images.unsplash.com/photo-1585659722983-3a675dabf23d?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Obsidian Black',
                'color_hex' => '#22252A',
                'purchase_price' => 1600000.00,
                'selling_price' => 2050000.00,
                'stock' => 3, // Low-Stock Example
                'minimum_stock' => 5,
            ],

            // 3. Laptop & Komputer
            [
                'category' => 'Laptop & Komputer',
                'sku' => 'ELC-LPT-001',
                'name' => 'Apple MacBook Pro 14 M3 Pro 18GB/512GB',
                'brand' => 'Apple',
                'image_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Space Black',
                'color_hex' => '#2E3136',
                'purchase_price' => 26500000.00,
                'selling_price' => 30999000.00,
                'stock' => 10,
                'minimum_stock' => 3,
            ],
            [
                'category' => 'Laptop & Komputer',
                'sku' => 'ELC-LPT-002',
                'name' => 'Laptop Gaming ASUS ROG Zephyrus G16',
                'brand' => 'ASUS',
                'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Eclipse Gray',
                'color_hex' => '#5A5D64',
                'purchase_price' => 24000000.00,
                'selling_price' => 27999000.00,
                'stock' => 2, // Low-Stock Example
                'minimum_stock' => 5,
            ],

            // 4. Smartphone & Tablet
            [
                'category' => 'Smartphone & Tablet',
                'sku' => 'ELC-SMP-001',
                'name' => 'iPhone 15 Pro Max 256GB Garansi Resmi',
                'brand' => 'Apple',
                'image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Natural Titanium',
                'color_hex' => '#9A958D',
                'purchase_price' => 19500000.00,
                'selling_price' => 22499000.00,
                'stock' => 12,
                'minimum_stock' => 4,
            ],
            [
                'category' => 'Smartphone & Tablet',
                'sku' => 'ELC-SMP-002',
                'name' => 'Samsung Galaxy S24 Ultra 5G 512GB',
                'brand' => 'Samsung',
                'image_url' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Titanium Violet',
                'color_hex' => '#584F68',
                'purchase_price' => 18500000.00,
                'selling_price' => 21499000.00,
                'stock' => 9,
                'minimum_stock' => 4,
            ],

            // 5. Kamera & Perangkat Pintar
            [
                'category' => 'Kamera & Perangkat Pintar',
                'sku' => 'ELC-CAM-001',
                'name' => 'Kamera Mirrorless Sony Alpha A7 IV Kit',
                'brand' => 'Sony',
                'image_url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80',
                'color_name' => 'Matte Black',
                'color_hex' => '#1A1A1A',
                'purchase_price' => 28000000.00,
                'selling_price' => 32499000.00,
                'stock' => 2, // Low-Stock Example
                'minimum_stock' => 4,
            ],
        ];

        foreach ($products as $item) {
            $cat = $categories->get($item['category']);

            Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'category_id' => $cat ? $cat->id : 1,
                    'sku' => $item['sku'],
                    'name' => $item['name'],
                    'brand' => $item['brand'],
                    'image_url' => $item['image_url'],
                    'color_name' => $item['color_name'],
                    'color_hex' => $item['color_hex'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'stock' => $item['stock'],
                    'minimum_stock' => $item['minimum_stock'],
                ]
            );
        }
    }
}
