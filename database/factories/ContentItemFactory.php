<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContentItem>
 */
class ContentItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => ContentType::InternalPost->value,
            'status' => ContentStatus::Draft->value,
            'author_id' => User::factory(),
            'approved_at' => null,
            'published_at' => null,
            'show_likes' => true,
            'share_on_publish' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Published->value,
            'approved_at' => now(),
            'published_at' => now(),
        ]);
    }

    public function internalPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContentType::InternalPost->value,
        ]);
    }

    public function externalPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContentType::ExternalPost->value,
        ]);
    }

    public function communityLink(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContentType::CommunityLink->value,
        ]);
    }
}
