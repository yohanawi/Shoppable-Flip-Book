<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Flipbook;
use App\Models\FlipbookPage;
use App\Models\Hotspot;
use Illuminate\Database\Seeder;

class FlipbookSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample products
        $products = [
            [
                'name' => 'Wireless Headphones',
                'slug' => 'wireless-headphones',
                'sku' => 'WH-001',
                'description' => 'Premium wireless headphones with noise cancellation',
                'short_description' => 'Premium wireless headphones',
                'price' => 149.99,
                'stock_quantity' => 50,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Smart Watch',
                'slug' => 'smart-watch',
                'sku' => 'SW-002',
                'description' => 'Feature-packed smartwatch with fitness tracking',
                'short_description' => 'Feature-packed smartwatch',
                'price' => 299.99,
                'stock_quantity' => 30,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Laptop Backpack',
                'slug' => 'laptop-backpack',
                'sku' => 'LB-003',
                'description' => 'Durable laptop backpack with multiple compartments',
                'short_description' => 'Durable laptop backpack',
                'price' => 79.99,
                'stock_quantity' => 100,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'USB-C Hub',
                'slug' => 'usbc-hub',
                'sku' => 'UH-004',
                'description' => '7-in-1 USB-C hub with HDMI and SD card reader',
                'short_description' => '7-in-1 USB-C hub',
                'price' => 49.99,
                'stock_quantity' => 75,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Bluetooth Speaker',
                'slug' => 'bluetooth-speaker',
                'sku' => 'BS-005',
                'description' => 'Portable waterproof bluetooth speaker',
                'short_description' => 'Portable bluetooth speaker',
                'price' => 89.99,
                'stock_quantity' => 60,
                'in_stock' => true,
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        $this->command->info('Sample products created successfully!');
    }
}
