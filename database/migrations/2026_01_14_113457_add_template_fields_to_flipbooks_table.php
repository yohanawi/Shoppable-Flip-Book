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
        Schema::table('flipbooks', function (Blueprint $table) {
            $table->string('template_type')->default('slicer')->after('description'); // page_management, page_flip_physics, slicer
            $table->json('template_config')->nullable()->after('template_type'); // Store template-specific configuration
            $table->json('page_structure')->nullable()->after('template_config'); // For page management: order, names, locks, visibility
            $table->json('flip_physics')->nullable()->after('page_structure'); // For flip physics: speed, curl, shadow, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flipbooks', function (Blueprint $table) {
            $table->dropColumn(['template_type', 'template_config', 'page_structure', 'flip_physics']);
        });
    }
};
