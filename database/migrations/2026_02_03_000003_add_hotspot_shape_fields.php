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
            $table->enum('shape_type', ['rectangle', 'polygon', 'freeform'])->default('rectangle')->after('type');
            $table->json('coordinates')->nullable()->after('shape_type'); // For polygon and freeform
            $table->string('action_type')->default('link')->after('coordinates'); // link, internal_page, popup_image, popup_video
            $table->integer('target_page_number')->nullable()->after('action_type'); // For internal page links
            $table->string('popup_media_url')->nullable()->after('target_page_number'); // For popup image/video
            $table->enum('popup_type', ['image', 'video', 'content'])->nullable()->after('popup_media_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flipbook_hotspots', function (Blueprint $table) {
            $table->dropColumn([
                'shape_type',
                'coordinates',
                'action_type',
                'target_page_number',
                'popup_media_url',
                'popup_type'
            ]);
        });
    }
};
