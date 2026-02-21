<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            $this->call(LocalDemoSeeder::class);

            return;
        }

        User::factory()->create([
            'name' => 'Daniel Rubango',
            'email' => 'danielrubango@gmail.com',
            'role' => UserRole::Admin->value,
            'preferred_locale' => 'fr',
        ]);
    }
}
