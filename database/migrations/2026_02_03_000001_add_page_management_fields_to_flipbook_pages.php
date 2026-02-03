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
        Schema::table('flipbook_pages', function (Blueprint $table) {
            $table->string('custom_name')->nullable()->after('page_number');
            $table->integer('display_order')->default(0)->after('custom_name');
            $table->boolean('is_locked')->default(false)->after('display_order');
            $table->boolean('is_hidden')->default(false)->after('is_locked');

            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flipbook_pages', function (Blueprint $table) {
            $table->dropColumn(['custom_name', 'display_order', 'is_locked', 'is_hidden']);
        });
    }
};
