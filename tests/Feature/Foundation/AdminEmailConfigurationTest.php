<?php

use App\Enums\UserRole;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LocalDemoSeeder;

test('database seeder uses admin email from app config', function () {
    config()->set('app.admin_email', 'owner@test.local');

    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseHas('users', [
        'email' => 'owner@test.local',
        'role' => UserRole::Admin->value,
    ]);
});

test('local demo seeder uses admin email from app config', function () {
    config()->set('app.admin_email', 'demo-owner@test.local');

    $this->seed(LocalDemoSeeder::class);

    $this->assertDatabaseHas('users', [
        'email' => 'demo-owner@test.local',
        'role' => UserRole::Admin->value,
    ]);
});
