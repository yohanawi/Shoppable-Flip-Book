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
        Schema::create('flipbooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('pdf_path'); // Original PDF file path
            $table->string('thumbnail')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('flipbook_templates')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Creator
            $table->integer('total_pages')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_public')->default(true);
            $table->json('settings')->nullable(); // Custom settings (zoom, download, etc.)
            $table->integer('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flipbooks');
    }
};
