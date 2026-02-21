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
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('status');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('show_likes')->default(true);
            $table->boolean('share_on_publish')->default(false);
            $table->unsignedBigInteger('reads_count')->default(0);
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['type', 'status']);
            $table->index(['reads_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
};
