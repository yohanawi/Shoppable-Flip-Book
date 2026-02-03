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
        Schema::create('flipbook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flipbook_id')->constrained()->cascadeOnDelete();
            $table->integer('page_number');
            $table->string('image_path')->nullable(); // Converted image path - nullable until processed
            $table->string('thumbnail_path')->nullable();
            $table->integer('width')->nullable(); // Page dimensions
            $table->integer('height')->nullable();
            $table->text('text_content')->nullable(); // OCR extracted text for search
            $table->json('metadata')->nullable(); // Additional page metadata
            $table->timestamps();

            $table->unique(['flipbook_id', 'page_number']);
            $table->index('flipbook_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flipbook_pages');
    }
};
