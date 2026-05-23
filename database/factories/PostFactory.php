<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->sentence(20),
            'content' => $this->faker->paragraphs(5, true),
            'status' => PostStatus::Draft,
            'featured' => false,
            'view_count' => 0,
            'published_at' => null,
            'meta_description' => $this->faker->sentence(15),
            'meta_keywords' => null,
            'og_image' => null,
        ];
    }

    /** Post is a saved draft (default). */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    /** Post is live and publicly visible. */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    /** Post is archived (hidden from public). */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Archived,
            'published_at' => now()->subDays(rand(60, 365)),
        ]);
    }

    /** Post is scheduled to publish in the future. */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => now()->addDays(rand(1, 14)),
        ]);
    }

    /** Post is featured. */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }
}
