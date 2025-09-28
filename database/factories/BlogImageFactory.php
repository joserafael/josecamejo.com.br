<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogImage>
 */
class BlogImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        $filename = $this->faker->uuid() . '.jpg';
        $originalFilename = $this->faker->words(2, true) . '.jpg';
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->optional()->paragraph(),
            'alt_text' => $this->faker->sentence(),
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'path' => 'blog/images/' . $filename,
            'mime_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(50000, 2000000), // 50KB to 2MB
            'width' => $this->faker->numberBetween(400, 1920),
            'height' => $this->faker->numberBetween(300, 1080),
            'language' => $this->faker->randomElement(['en', 'es', 'pt']),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the image is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the image is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the language for the image.
     */
    public function language(string $language): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => $language,
        ]);
    }

    /**
     * Create an English image.
     */
    public function english(): static
    {
        return $this->language('en');
    }

    /**
     * Create a Spanish image.
     */
    public function spanish(): static
    {
        return $this->language('es');
    }

    /**
     * Create a Portuguese image.
     */
    public function portuguese(): static
    {
        return $this->language('pt');
    }

    /**
     * Create a PNG image.
     */
    public function png(): static
    {
        $filename = $this->faker->uuid() . '.png';
        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => 'blog/images/' . $filename,
            'mime_type' => 'image/png',
        ]);
    }

    /**
     * Create a WebP image.
     */
    public function webp(): static
    {
        $filename = $this->faker->uuid() . '.webp';
        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => 'blog/images/' . $filename,
            'mime_type' => 'image/webp',
        ]);
    }
}
