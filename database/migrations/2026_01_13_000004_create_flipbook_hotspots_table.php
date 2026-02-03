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
        Schema::create('flipbook_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flipbook_page_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // link, product, video, popup, internal, external
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Position and dimensions (percentage-based for responsive)
            $table->decimal('x_position', 8, 4); // X coordinate (%)
            $table->decimal('y_position', 8, 4); // Y coordinate (%)
            $table->decimal('width', 8, 4); // Width (%)
            $table->decimal('height', 8, 4); // Height (%)

            // Target information
            $table->string('target_url')->nullable(); // External URL
            $table->string('target_route')->nullable(); // Internal route name
            $table->json('target_params')->nullable(); // Route parameters
            $table->unsignedBigInteger('product_id')->nullable(); // Product reference
            $table->text('popup_content')->nullable(); // HTML content for popups

            // Styling
            $table->string('icon')->nullable(); // Icon to display
            $table->string('color')->nullable(); // Hotspot color
            $table->string('animation')->nullable(); // Animation type

            // Behavior
            $table->boolean('is_active')->default(true);
            $table->string('target_type')->default('_blank'); // _blank, _self, modal, cart
            $table->integer('display_order')->default(0);

            $table->timestamps();

            $table->index('flipbook_page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flipbook_hotspots');
    }
};
