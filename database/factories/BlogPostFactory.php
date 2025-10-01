<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);
        $content = $this->faker->paragraphs(8, true);
        $excerpt = $this->faker->text(200);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $excerpt,
            'content' => $content,
            'featured_image' => $this->faker->imageUrl(800, 600, 'technology', true),
            'meta_title' => $this->faker->sentence(4, true),
            'meta_description' => $this->faker->text(160),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'published_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 year', '+1 month'),
            'language' => $this->faker->randomElement(['pt', 'en', 'es']),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'allow_comments' => $this->faker->boolean(80), // 80% chance of allowing comments
            'views_count' => $this->faker->numberBetween(0, 10000),
            'sort_order' => $this->faker->numberBetween(1, 1000),
            'user_id' => User::factory(),
            'blog_category_id' => BlogCategory::factory(),
            'blog_subcategory_id' => function (array $attributes) {
                return BlogSubcategory::where('blog_category_id', $attributes['blog_category_id'])->inRandomOrder()->first()?->id;
            },
        ];
    }

    /**
     * Indicate that the blog post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the blog post is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the blog post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the blog post is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'published_at' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
        ]);
    }
}
