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
            $table->string('headline')->nullable()->after('public_profile_slug');
            $table->text('bio')->nullable()->after('headline');
            $table->string('location')->nullable()->after('bio');
            $table->string('website_url', 2048)->nullable()->after('location');
            $table->string('linkedin_url', 2048)->nullable()->after('website_url');
            $table->string('x_url', 2048)->nullable()->after('linkedin_url');
            $table->string('github_url', 2048)->nullable()->after('x_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'headline',
                'bio',
                'location',
                'website_url',
                'linkedin_url',
                'x_url',
                'github_url',
            ]);
        });
    }
};
