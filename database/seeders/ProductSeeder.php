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
            // Laptop & Notebook
            [
                'category' => 'Laptop & Notebook',
                'sku' => 'ELC-LPT-001',
                'name' => 'ASUS ZenBook 14 OLED UX3405 Core Ultra 7',
                'brand' => 'ASUS',
                'purchase_price' => 15500000.00,
                'selling_price' => 17999000.00,
                'stock' => 12,
                'minimum_stock' => 5,
            ],
            [
                'category' => 'Laptop & Notebook',
                'sku' => 'ELC-LPT-002',
                'name' => 'Lenovo Legion Pro 5 16IRX8 RTX 4060',
                'brand' => 'Lenovo',
                'purchase_price' => 21000000.00,
                'selling_price' => 24500000.00,
                'stock' => 3, // LOW STOCK ALERT
                'minimum_stock' => 5,
            ],
            [
                'category' => 'Laptop & Notebook',
                'sku' => 'ELC-LPT-003',
                'name' => 'Apple MacBook Air M3 13 Inch 256GB Space Grey',
                'brand' => 'Apple',
                'purchase_price' => 16000000.00,
                'selling_price' => 18499000.00,
                'stock' => 8,
                'minimum_stock' => 4,
            ],

            // Smartphone & Tablet
            [
                'category' => 'Smartphone & Tablet',
                'sku' => 'ELC-SMP-001',
                'name' => 'Samsung Galaxy S24 Ultra 256GB Titanium Gray',
                'brand' => 'Samsung',
                'purchase_price' => 18000000.00,
                'selling_price' => 20999000.00,
                'stock' => 15,
                'minimum_stock' => 5,
            ],
            [
                'category' => 'Smartphone & Tablet',
                'sku' => 'ELC-SMP-002',
                'name' => 'iPhone 15 Pro 128GB Natural Titanium',
                'brand' => 'Apple',
                'purchase_price' => 17200000.00,
                'selling_price' => 19999000.00,
                'stock' => 2, // LOW STOCK ALERT
                'minimum_stock' => 5,
            ],
            [
                'category' => 'Smartphone & Tablet',
                'sku' => 'ELC-SMP-003',
                'name' => 'Xiaomi 14 12GB/512GB Leica Summilux Optics',
                'brand' => 'Xiaomi',
                'purchase_price' => 10500000.00,
                'selling_price' => 12499000.00,
                'stock' => 10,
                'minimum_stock' => 4,
            ],

            // Komponen PC & Hardware
            [
                'category' => 'Komponen PC & Hardware',
                'sku' => 'ELC-CMP-001',
                'name' => 'Gigabyte GeForce RTX 4070 Ti Super Gaming OC 16GB',
                'brand' => 'Gigabyte',
                'purchase_price' => 13200000.00,
                'selling_price' => 15200000.00,
                'stock' => 7,
                'minimum_stock' => 3,
            ],
            [
                'category' => 'Komponen PC & Hardware',
                'sku' => 'ELC-CMP-002',
                'name' => 'AMD Ryzen 7 7800X3D 8-Core 16-Thread AM5',
                'brand' => 'AMD',
                'purchase_price' => 6100000.00,
                'selling_price' => 7200000.00,
                'stock' => 2, // LOW STOCK ALERT
                'minimum_stock' => 4,
            ],
            [
                'category' => 'Komponen PC & Hardware',
                'sku' => 'ELC-CMP-003',
                'name' => 'Kingston Fury Beast DDR5 32GB (2x16GB) 6000MHz RGB',
                'brand' => 'Kingston',
                'purchase_price' => 1750000.00,
                'selling_price' => 2150000.00,
                'stock' => 20,
                'minimum_stock' => 5,
            ],

            // Aksesoris & Periferal
            [
                'category' => 'Aksesoris & Periferal',
                'sku' => 'ELC-ACC-001',
                'name' => 'Logitech MX Master 3S Wireless Performance Mouse',
                'brand' => 'Logitech',
                'purchase_price' => 1250000.00,
                'selling_price' => 1599000.00,
                'stock' => 25,
                'minimum_stock' => 6,
            ],
            [
                'category' => 'Aksesoris & Periferal',
                'sku' => 'ELC-ACC-002',
                'name' => 'Keychron Q1 Pro Wireless Custom Mechanical Keyboard',
                'brand' => 'Keychron',
                'purchase_price' => 2700000.00,
                'selling_price' => 3299000.00,
                'stock' => 9,
                'minimum_stock' => 3,
            ],

            // Monitor & Display
            [
                'category' => 'Monitor & Display',
                'sku' => 'ELC-MON-001',
                'name' => 'LG UltraGear 27GP850-B 27 Inch Nano IPS QHD 165Hz',
                'brand' => 'LG',
                'purchase_price' => 4800000.00,
                'selling_price' => 5799000.00,
                'stock' => 11,
                'minimum_stock' => 4,
            ],
            [
                'category' => 'Monitor & Display',
                'sku' => 'ELC-MON-002',
                'name' => 'Dell UltraSharp U2723QE 27 Inch 4K UHD IPS Black',
                'brand' => 'Dell',
                'purchase_price' => 9200000.00,
                'selling_price' => 10899000.00,
                'stock' => 1, // LOW STOCK ALERT
                'minimum_stock' => 3,
            ],
        ];

        foreach ($products as $item) {
            $categoryName = $item['category'];
            unset($item['category']);

            $category = $categories[$categoryName] ?? null;
            if ($category) {
                $item['category_id'] = $category->id;
                Product::updateOrCreate(
                    ['sku' => $item['sku']],
                    $item
                );
            }
        }
    }
}
