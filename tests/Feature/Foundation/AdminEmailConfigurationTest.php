<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LocalDemoSeeder;
use Illuminate\Support\Facades\Hash;

test('database seeder uses admin email from app config', function () {
    config()->set('app.admin_email', 'owner@test.local');
    config()->set('app.admin_password', 'owner-secret');

    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseHas('users', [
        'email' => 'owner@test.local',
        'role' => UserRole::Admin->value,
    ]);

    $admin = User::query()->where('email', 'owner@test.local')->firstOrFail();

    expect(Hash::check('owner-secret', $admin->password))->toBeTrue();
});

test('local demo seeder uses admin email from app config', function () {
    config()->set('app.admin_email', 'demo-owner@test.local');
    config()->set('app.admin_password', 'demo-secret');

    $this->seed(LocalDemoSeeder::class);

    $this->assertDatabaseHas('users', [
        'email' => 'demo-owner@test.local',
        'role' => UserRole::Admin->value,
    ]);

    $admin = User::query()->where('email', 'demo-owner@test.local')->firstOrFail();

    expect(Hash::check('demo-secret', $admin->password))->toBeTrue();
});
