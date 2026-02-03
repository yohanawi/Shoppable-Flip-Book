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
        Schema::create('flipbook_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flipbook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flipbook_page_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('flipbook_hotspot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('event_type'); // view, page_turn, hotspot_click, download, share
            $table->string('session_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->json('metadata')->nullable(); // Additional event data
            $table->timestamp('created_at');

            $table->index(['flipbook_id', 'event_type', 'created_at']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flipbook_analytics');
    }
};
