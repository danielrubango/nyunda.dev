<?php

namespace Database\Factories;

use App\Models\ContentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialShareLog>
 */
class SocialShareLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content_item_id' => ContentItem::factory()->published()->internalPost(),
            'platform' => fake()->randomElement(['x', 'linkedin']),
            'status' => fake()->randomElement(['success', 'failed', 'skipped']),
            'shared_url' => fake()->url(),
            'request_payload' => ['text' => fake()->sentence()],
            'response_payload' => ['id' => fake()->uuid()],
            'error_message' => null,
            'attempted_at' => now(),
        ];
    }
}
