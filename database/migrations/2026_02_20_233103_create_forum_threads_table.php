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
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->longText('body_markdown');
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('hidden_at')->nullable();
            $table->foreignId('hidden_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('best_reply_id')->nullable();
            $table->timestamps();

            $table->index(['locale', 'created_at']);
            $table->index(['author_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_threads');
    }
};
