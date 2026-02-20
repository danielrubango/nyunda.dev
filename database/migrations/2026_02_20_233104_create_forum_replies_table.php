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
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_thread_id')->constrained('forum_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body_markdown');
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('hidden_at')->nullable();
            $table->foreignId('hidden_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['forum_thread_id', 'is_hidden', 'created_at']);
        });

        Schema::table('forum_threads', function (Blueprint $table) {
            $table->foreign('best_reply_id')
                ->references('id')
                ->on('forum_replies')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropForeign(['best_reply_id']);
        });

        Schema::dropIfExists('forum_replies');
    }
};
