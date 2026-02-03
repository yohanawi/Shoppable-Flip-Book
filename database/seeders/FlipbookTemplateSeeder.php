<?php

namespace Database\Seeders;

use App\Models\FlipbookTemplate;
use Illuminate\Database\Seeder;

class FlipbookTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Classic',
                'slug' => 'classic',
                'description' => 'Traditional page flip with smooth transitions',
                'settings' => [
                    'animation' => 'slide',
                    'duration' => 1000,
                    'autoCenter' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => '3D Flip',
                'slug' => '3d-flip',
                'description' => '3D page turning effect with depth',
                'settings' => [
                    'animation' => '3d',
                    'duration' => 1200,
                    'elevation' => 100,
                    'gradients' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Magazine Style',
                'slug' => 'magazine',
                'description' => 'Modern magazine layout with smooth page turns',
                'settings' => [
                    'animation' => 'slide',
                    'duration' => 800,
                    'acceleration' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Catalog',
                'slug' => 'catalog',
                'description' => 'Product catalog style with zoom support',
                'settings' => [
                    'animation' => 'slide',
                    'duration' => 1000,
                    'zoom' => true,
                    'thumbnails' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Simple Scroll',
                'slug' => 'scroll',
                'description' => 'Vertical scroll layout instead of page turns',
                'settings' => [
                    'layout' => 'scroll',
                    'continuous' => true,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            FlipbookTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }

        $this->command->info('Flipbook templates seeded successfully!');
    }
}
