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
        Schema::table('flipbook_hotspots', function (Blueprint $table) {
            // Enhanced color options
            $table->string('default_color')->default('#667eea')->after('color');
            $table->string('hover_color')->default('#764ba2')->after('default_color');
            $table->string('active_color')->default('#50cd89')->after('hover_color');

            // Enhanced interaction types
            $table->string('interaction_type')->default('link')->after('type');
            // Values: internal_link, external_link, popup_product, popup_image, popup_video

            // Product popup fields
            $table->string('thumbnail_image')->nullable()->after('interaction_type');
            $table->decimal('price', 10, 2)->nullable()->after('product_name');
            $table->integer('discount_percentage')->nullable()->after('price');
            $table->integer('stock_quantity')->nullable()->after('discount_percentage');
            $table->string('action_url')->nullable()->after('target_url');
            $table->boolean('open_new_tab')->default(true)->after('action_url');

            // Media fields for popup image/video
            $table->string('popup_image')->nullable()->after('popup_content');
            $table->string('popup_video')->nullable()->after('popup_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flipbook_hotspots', function (Blueprint $table) {
            $table->dropColumn([
                'default_color',
                'hover_color',
                'active_color',
                'interaction_type',
                'thumbnail_image',
                'price',
                'discount_percentage',
                'stock_quantity',
                'action_url',
                'open_new_tab',
                'popup_image',
                'popup_video'
            ]);
        });
    }
};
