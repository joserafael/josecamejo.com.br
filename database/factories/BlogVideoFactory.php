<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogVideo>
 */
class BlogVideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        $filename = $this->faker->uuid() . '.mp4';
        $originalFilename = $this->faker->words(2, true) . '.mp4';
        $thumbnailFilename = $this->faker->uuid() . '.jpg';
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->optional()->paragraph(),
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'path' => 'blog/videos/' . $filename,
            'mime_type' => 'video/mp4',
            'file_size' => $this->faker->numberBetween(5000000, 100000000), // 5MB to 100MB
            'width' => $this->faker->randomElement([1280, 1920, 854, 640]),
            'height' => $this->faker->randomElement([720, 1080, 480, 360]),
            'duration' => $this->faker->numberBetween(30, 3600), // 30 seconds to 1 hour
            'thumbnail_path' => 'blog/videos/thumbnails/' . $thumbnailFilename,
            'language' => $this->faker->randomElement(['en', 'es', 'pt']),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the video is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the video is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the language for the video.
     */
    public function language(string $language): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => $language,
        ]);
    }

    /**
     * Create an English video.
     */
    public function english(): static
    {
        return $this->language('en');
    }

    /**
     * Create a Spanish video.
     */
    public function spanish(): static
    {
        return $this->language('es');
    }

    /**
     * Create a Portuguese video.
     */
    public function portuguese(): static
    {
        return $this->language('pt');
    }

    /**
     * Create a WebM video.
     */
    public function webm(): static
    {
        $filename = $this->faker->uuid() . '.webm';
        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => 'blog/videos/' . $filename,
            'mime_type' => 'video/webm',
        ]);
    }

    /**
     * Create an AVI video.
     */
    public function avi(): static
    {
        $filename = $this->faker->uuid() . '.avi';
        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => 'blog/videos/' . $filename,
            'mime_type' => 'video/avi',
        ]);
    }

    /**
     * Create a MOV video.
     */
    public function mov(): static
    {
        $filename = $this->faker->uuid() . '.mov';
        return $this->state(fn (array $attributes) => [
            'filename' => $filename,
            'path' => 'blog/videos/' . $filename,
            'mime_type' => 'video/quicktime',
        ]);
    }

    /**
     * Create a short video (under 2 minutes).
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration' => $this->faker->numberBetween(30, 120),
        ]);
    }

    /**
     * Create a long video (over 10 minutes).
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration' => $this->faker->numberBetween(600, 3600),
        ]);
    }

    /**
     * Create an HD video.
     */
    public function hd(): static
    {
        return $this->state(fn (array $attributes) => [
            'width' => 1280,
            'height' => 720,
        ]);
    }

    /**
     * Create a Full HD video.
     */
    public function fullHd(): static
    {
        return $this->state(fn (array $attributes) => [
            'width' => 1920,
            'height' => 1080,
        ]);
    }
}
