<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterEdition>
 */
class NewsletterEditionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_fr' => fake()->sentence(6),
            'subject_en' => fake()->sentence(6),
            'intro_fr' => fake()->paragraph(),
            'intro_en' => fake()->paragraph(),
            'content_item_ids' => [],
            'status' => 'draft',
            'recipients_count' => 0,
            'sent_count' => 0,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
        ]);
    }
}
