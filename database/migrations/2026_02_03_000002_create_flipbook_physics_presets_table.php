<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flipbook_physics_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('parameters'); // Store all physics parameters
            $table->timestamps();
        });

        // Insert default presets
        DB::table('flipbook_physics_presets')->insert([
            [
                'name' => 'Realistic',
                'description' => 'Natural page flip with realistic physics',
                'is_default' => true,
                'parameters' => json_encode([
                    'duration' => 800,
                    'acceleration' => 1.5,
                    'hardness' => 10,
                    'elevation' => 300,
                    'corners' => 'forward',
                    'startFlipAngle' => 0,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fast',
                'description' => 'Quick page transitions',
                'is_default' => false,
                'parameters' => json_encode([
                    'duration' => 400,
                    'acceleration' => 2.5,
                    'hardness' => 5,
                    'elevation' => 200,
                    'corners' => 'forward',
                    'startFlipAngle' => 0,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smooth',
                'description' => 'Gentle and smooth page flipping',
                'is_default' => false,
                'parameters' => json_encode([
                    'duration' => 1200,
                    'acceleration' => 1.0,
                    'hardness' => 15,
                    'elevation' => 400,
                    'corners' => 'forward',
                    'startFlipAngle' => 0,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rigid',
                'description' => 'Stiff pages with minimal flex',
                'is_default' => false,
                'parameters' => json_encode([
                    'duration' => 600,
                    'acceleration' => 2.0,
                    'hardness' => 20,
                    'elevation' => 150,
                    'corners' => 'forward',
                    'startFlipAngle' => 0,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flipbook_physics_presets');
    }
};
