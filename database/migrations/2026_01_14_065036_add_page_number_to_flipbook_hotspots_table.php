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
            $table->integer('page_number')->nullable()->after('flipbook_page_id');
            $table->foreignId('flipbook_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('flipbook_page_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flipbook_hotspots', function (Blueprint $table) {
            // Drop foreign key constraint before dropping the column
            $table->dropForeign(['flipbook_id']);
            $table->dropColumn(['page_number', 'flipbook_id']);
            $table->foreignId('flipbook_page_id')->nullable(false)->change();
        });
    }
};
