<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Tag;
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
        $adminEmail = (string) config('app.admin_email');
        $adminPassword = (string) config('app.admin_password');

        if (app()->isLocal()) {
            $this->call(LocalDemoSeeder::class);

            return;
        }

        $this->seedProductionTags();

        $admin = User::query()->firstOrCreate([
            'email' => $adminEmail,
        ], [
            'name' => 'Daniel Rubango',
            'password' => $adminPassword,
            'role' => UserRole::Admin->value,
            'preferred_locale' => 'fr',
        ]);

        $admin->forceFill([
            'name' => 'Daniel Rubango',
            'password' => $adminPassword,
            'role' => UserRole::Admin->value,
            'preferred_locale' => 'fr',
        ])->save();
    }

    /**
     * @return list<array{name: string, slug: string}>
     */
    private function productionTags(): array
    {
        return [
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'Livewire', 'slug' => 'livewire'],
            ['name' => 'Filament', 'slug' => 'filament'],
            ['name' => 'Architecture', 'slug' => 'architecture'],
            ['name' => 'API', 'slug' => 'api'],
            ['name' => 'Testing', 'slug' => 'testing'],
            ['name' => 'Performance', 'slug' => 'performance'],
            ['name' => 'MySQL', 'slug' => 'mysql'],
            ['name' => 'Redis', 'slug' => 'redis'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Frontend', 'slug' => 'frontend'],
        ];
    }

    private function seedProductionTags(): void
    {
        foreach ($this->productionTags() as $index => $tag) {
            Tag::query()->updateOrCreate([
                'slug' => $tag['slug'],
            ], [
                'name' => $tag['name'],
                'sort_order' => $index + 1,
            ]);
        }
    }
}
