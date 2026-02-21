<?php

use App\Models\Tag;
use Database\Seeders\DatabaseSeeder;

test('database seeder seeds baseline production tags', function () {
    config()->set('app.admin_email', 'owner@test.local');

    $this->seed(DatabaseSeeder::class);

    expect(Tag::query()->count())->toBe(12);

    $this->assertDatabaseHas('tags', [
        'slug' => 'laravel',
        'name' => 'Laravel',
        'sort_order' => 1,
    ]);

    $this->assertDatabaseHas('tags', [
        'slug' => 'frontend',
        'name' => 'Frontend',
        'sort_order' => 12,
    ]);
});
