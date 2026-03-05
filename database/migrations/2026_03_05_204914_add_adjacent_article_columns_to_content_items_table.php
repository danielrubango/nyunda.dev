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
        Schema::table('content_items', function (Blueprint $table) {
            $table->foreignId('prev_article_id')
                ->nullable()
                ->after('is_featured')
                ->constrained('content_items')
                ->nullOnDelete();

            $table->foreignId('next_article_id')
                ->nullable()
                ->after('prev_article_id')
                ->constrained('content_items')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('next_article_id');
            $table->dropConstrainedForeignId('prev_article_id');
        });
    }
};
