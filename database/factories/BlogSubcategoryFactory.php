<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogSubcategory>
 */
class BlogSubcategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BlogSubcategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        
        return [
            'name' => $name,
            'slug' => BlogSubcategory::generateSlug($name),
            'description' => $this->faker->sentence(),
            'language' => $this->faker->randomElement(['pt', 'en', 'es']),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'sort_order' => $this->faker->numberBetween(1, 100),
            'blog_category_id' => BlogCategory::factory(),
        ];
    }

    /**
     * Indicate that the subcategory is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the subcategory is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the language for the subcategory.
     */
    public function language(string $language): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => $language,
        ]);
    }

    /**
     * Set the category for the subcategory.
     */
    public function forCategory(BlogCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'blog_category_id' => $category->id,
        ]);
    }
}