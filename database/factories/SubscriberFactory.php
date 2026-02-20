<?php

namespace Database\Factories;

use App\Enums\SubscriberStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'status' => SubscriberStatus::Pending->value,
            'confirmation_token' => Str::random(40),
            'confirmed_at' => null,
            'locale' => fake()->randomElement(['fr', 'en']),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubscriberStatus::Confirmed->value,
            'confirmation_token' => null,
            'confirmed_at' => now(),
        ]);
    }
}
