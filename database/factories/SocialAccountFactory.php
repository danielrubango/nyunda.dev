<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider' => fake()->randomElement(['google', 'linkedin']),
            'provider_user_id' => fake()->unique()->numerify('provider-########'),
            'provider_email' => fake()->safeEmail(),
            'provider_name' => fake()->name(),
            'avatar_url' => fake()->imageUrl(),
        ];
    }
}
