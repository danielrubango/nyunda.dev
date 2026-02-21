<?php

namespace Database\Factories;

use App\Models\ContentItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentRead>
 */
class ContentReadFactory extends Factory
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
            'visitor_fingerprint' => null,
            'counted_at' => now(),
        ];
    }
}
