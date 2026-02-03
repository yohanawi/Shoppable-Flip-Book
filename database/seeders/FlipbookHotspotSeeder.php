<?php

namespace Database\Seeders;

use App\Models\Flipbook;
use App\Models\FlipbookHotspot;
use App\Models\Product;
use Illuminate\Database\Seeder;

class FlipbookHotspotSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first flipbook
        $flipbook = Flipbook::first();

        if (!$flipbook) {
            $this->command->warn('⚠️ No flipbooks found. Create a flipbook first.');
            return;
        }

        // Clear existing hotspots for this flipbook
        FlipbookHotspot::where('flipbook_id', $flipbook->id)->delete();

        $products = Product::take(3)->get();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️ No products found. Run ProductSeeder first.');
            return;
        }

        foreach ($products as $index => $product) {
            FlipbookHotspot::create([
                'flipbook_id' => $flipbook->id,
                'page_number' => 1,
                'type' => 'product',
                'title' => $product->name,
                'description' => $product->short_description,
                'x_position' => 20 + ($index * 25),
                'y_position' => 30 + ($index * 15),
                'width' => 15,
                'height' => 15,
                'product_id' => $product->id,
                'color' => '#28a745',
                'animation' => 'pulse',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Added ' . $products->count() . ' product hotspots to "' . $flipbook->title . '" (Page 1)');
    }
}
