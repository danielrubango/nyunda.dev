<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumThread>
 */
class ForumThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'author_id' => User::factory(),
            'locale' => fake()->randomElement(['fr', 'en']),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'body_markdown' => fake()->paragraphs(3, true),
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
            'best_reply_id' => null,
        ];
    }
}
