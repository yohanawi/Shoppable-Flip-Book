<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $electronics = ProductCategory::firstOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'description' => 'Latest electronic gadgets and devices',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $fashion = ProductCategory::firstOrCreate(
            ['slug' => 'fashion'],
            [
                'name' => 'Fashion',
                'description' => 'Trendy fashion and accessories',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $homeDecor = ProductCategory::firstOrCreate(
            ['slug' => 'home-decor'],
            [
                'name' => 'Home & Decor',
                'description' => 'Beautiful items for your home',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Create sample products
        Product::create([
            'name' => 'Wireless Bluetooth Headphones',
            'slug' => 'wireless-bluetooth-headphones',
            'sku' => 'WH-BT-001',
            'short_description' => 'Premium wireless headphones with noise cancellation',
            'description' => 'Experience crystal-clear audio with our premium wireless Bluetooth headphones. Features active noise cancellation, 30-hour battery life, and comfortable over-ear design.',
            'price' => 199.99,
            'sale_price' => 149.99,
            'stock_quantity' => 50,
            'in_stock' => true,
            'is_featured' => true,
            'category_id' => $electronics->id,
            'images' => json_encode([
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=500&h=500&fit=crop',
            ]),
            'specifications' => json_encode([
                'Battery Life' => '30 hours',
                'Connectivity' => 'Bluetooth 5.0',
                'Weight' => '250g',
                'Color' => 'Black',
            ]),
        ]);

        Product::create([
            'name' => 'Smart Watch Pro',
            'slug' => 'smart-watch-pro',
            'sku' => 'SW-PRO-002',
            'short_description' => 'Advanced fitness tracking smartwatch',
            'description' => 'Track your health and fitness goals with our advanced smartwatch. Features heart rate monitoring, GPS, sleep tracking, and 7-day battery life.',
            'price' => 299.99,
            'sale_price' => 249.99,
            'stock_quantity' => 35,
            'in_stock' => true,
            'is_featured' => true,
            'category_id' => $electronics->id,
            'images' => json_encode([
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?w=500&h=500&fit=crop',
            ]),
            'specifications' => json_encode([
                'Display' => '1.4" AMOLED',
                'Battery' => '7 days',
                'Water Resistance' => '5ATM',
                'GPS' => 'Built-in',
            ]),
        ]);

        Product::create([
            'name' => 'Designer Leather Handbag',
            'slug' => 'designer-leather-handbag',
            'sku' => 'FH-LB-003',
            'short_description' => 'Elegant genuine leather handbag',
            'description' => 'Handcrafted from premium genuine leather, this elegant handbag combines style and functionality. Perfect for any occasion.',
            'price' => 349.99,
            'sale_price' => null,
            'stock_quantity' => 20,
            'in_stock' => true,
            'is_featured' => true,
            'category_id' => $fashion->id,
            'images' => json_encode([
                'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1591561954557-26941169b49e?w=500&h=500&fit=crop',
            ]),
            'specifications' => json_encode([
                'Material' => '100% Genuine Leather',
                'Dimensions' => '35cm x 25cm x 15cm',
                'Color' => 'Brown',
                'Closure' => 'Magnetic snap',
            ]),
        ]);

        Product::create([
            'name' => 'Minimalist Table Lamp',
            'slug' => 'minimalist-table-lamp',
            'sku' => 'HD-TL-004',
            'short_description' => 'Modern LED table lamp with touch control',
            'description' => 'Illuminate your space with this sleek minimalist LED table lamp. Features touch control, adjustable brightness, and energy-efficient LED technology.',
            'price' => 89.99,
            'sale_price' => 69.99,
            'stock_quantity' => 100,
            'in_stock' => true,
            'is_featured' => false,
            'category_id' => $homeDecor->id,
            'images' => json_encode([
                'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1541480601022-2308c0f02487?w=500&h=500&fit=crop',
            ]),
            'specifications' => json_encode([
                'Light Source' => 'LED',
                'Power' => '12W',
                'Brightness Levels' => '3',
                'Material' => 'Aluminum',
            ]),
        ]);

        Product::create([
            'name' => 'Ceramic Vase Set',
            'slug' => 'ceramic-vase-set',
            'sku' => 'HD-VS-005',
            'short_description' => 'Set of 3 handmade ceramic vases',
            'description' => 'Add a touch of elegance to your home with this beautiful set of handmade ceramic vases. Perfect for fresh flowers or as standalone decor pieces.',
            'price' => 129.99,
            'sale_price' => 99.99,
            'stock_quantity' => 45,
            'in_stock' => true,
            'is_featured' => false,
            'category_id' => $homeDecor->id,
            'images' => json_encode([
                'https://images.unsplash.com/photo-1578500494198-246f612d3b3d?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1565175142094-e6c3f8748f0f?w=500&h=500&fit=crop',
            ]),
            'specifications' => json_encode([
                'Material' => 'Ceramic',
                'Pieces' => '3 vases',
                'Heights' => '20cm, 25cm, 30cm',
                'Color' => 'White with gold trim',
            ]),
        ]);

        Product::create([
            'name' => 'Wireless Keyboard & Mouse Combo',
            'slug' => 'wireless-keyboard-mouse-combo',
            'sku' => 'EL-KM-006',
            'short_description' => 'Ergonomic wireless keyboard and mouse',
            'description' => 'Boost your productivity with this ergonomic wireless keyboard and mouse combo. Features quiet keys, comfortable design, and long battery life.',
            'price' => 79.99,
            'sale_price' => 59.99,
            'stock_quantity' => 75,
            'in_stock' => true,
            'is_featured' => false,
            'category_id' => $electronics->id,
            'images' => json_encode([
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&h=500&fit=crop',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?w=500&h=500&fit=crop',
            ]),
            'specifications' => json_encode([
                'Connectivity' => 'Wireless 2.4GHz',
                'Battery Life' => '12 months',
                'Keyboard Type' => 'Full-size',
                'Mouse DPI' => '1600',
            ]),
        ]);

        $this->command->info('✅ Created ' . Product::count() . ' sample products in ' . ProductCategory::count() . ' categories');
    }
}
