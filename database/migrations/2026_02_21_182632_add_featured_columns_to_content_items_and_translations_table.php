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
            $table->boolean('is_featured')->default(false)->after('share_on_publish');
            $table->index(['is_featured', 'published_at']);
        });

        Schema::table('content_translations', function (Blueprint $table) {
            $table->string('featured_image_url', 2048)->nullable()->after('external_og_image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropIndex(['is_featured', 'published_at']);
            $table->dropColumn('is_featured');
        });

        Schema::table('content_translations', function (Blueprint $table) {
            $table->dropColumn('featured_image_url');
        });
    }
};
