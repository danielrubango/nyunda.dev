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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password');
            $table->string('preferred_locale', 5)->default('fr')->after('role');
            $table->boolean('is_profile_public')->default(false)->after('preferred_locale');
            $table->string('public_profile_slug')->nullable()->unique()->after('is_profile_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['public_profile_slug']);
            $table->dropColumn([
                'role',
                'preferred_locale',
                'is_profile_public',
                'public_profile_slug',
            ]);
        });
    }
};
