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
            $table->string('visibility')->default('private')->after('is_published'); // public, private, unlisted
            $table->string('status')->default('draft')->after('visibility'); // draft, live, archived
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flipbooks', function (Blueprint $table) {
            $table->dropColumn(['visibility', 'status']);
        });
    }
};
