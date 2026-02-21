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
        if (Schema::hasColumn('tags', 'sort_order')) {
            return;
        }

        Schema::table('tags', function (Blueprint $table): void {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('slug')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('tags', 'sort_order')) {
            return;
        }

        Schema::table('tags', function (Blueprint $table): void {
            $table->dropIndex(['sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
