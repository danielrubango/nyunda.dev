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
        Schema::create('content_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_item_id')->constrained('content_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visitor_fingerprint', 64)->nullable();
            $table->timestamp('counted_at');
            $table->timestamps();

            $table->index(['content_item_id', 'counted_at']);
            $table->index(['content_item_id', 'user_id', 'counted_at']);
            $table->index(['content_item_id', 'visitor_fingerprint', 'counted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_reads');
    }
};
