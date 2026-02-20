<?php

namespace Database\Factories;

use App\Models\ContentItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_item_id' => ContentItem::factory()->published()->internalPost(),
            'user_id' => User::factory(),
            'body_markdown' => fake()->paragraph(),
            'is_hidden' => false,
            'hidden_at' => null,
            'hidden_by_id' => null,
        ];
    }
}
