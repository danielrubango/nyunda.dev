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
        Schema::create('content_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_item_id')->constrained('content_items')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt');
            $table->longText('body_markdown')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->text('external_description')->nullable();
            $table->string('external_site_name')->nullable();
            $table->string('external_og_image_url', 2048)->nullable();
            $table->timestamps();

            $table->unique(['content_item_id', 'locale']);
            $table->unique(['locale', 'slug']);
            $table->index(['content_item_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_translations');
    }
};
