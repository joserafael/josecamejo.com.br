<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\BlogImage;
use App\Models\BlogVideo;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have some categories, subcategories, tags, images, and videos
        $this->ensureRelatedData();

        // Create 20 blog posts with various states
        $blogPosts = collect();

        // Create 10 published posts
        $publishedPosts = BlogPost::factory()
            ->count(10)
            ->published()
            ->create();
        $blogPosts = $blogPosts->merge($publishedPosts);

        // Create 3 featured posts
        $featuredPosts = BlogPost::factory()
            ->count(3)
            ->published()
            ->featured()
            ->create();
        $blogPosts = $blogPosts->merge($featuredPosts);

        // Create 5 draft posts
        $draftPosts = BlogPost::factory()
            ->count(5)
            ->draft()
            ->create();
        $blogPosts = $blogPosts->merge($draftPosts);

        // Create 2 archived posts
        $archivedPosts = BlogPost::factory()
            ->count(2)
            ->archived()
            ->create();
        $blogPosts = $blogPosts->merge($archivedPosts);

        // Attach tags to posts (random 1-5 tags per post)
        $tags = BlogTag::all();
        if ($tags->isNotEmpty()) {
            $blogPosts->each(function ($post) use ($tags) {
                $randomTags = $tags->random(rand(1, min(5, $tags->count())));
                $post->tags()->attach($randomTags);
            });
        }

        // Attach images to posts (random 0-3 images per post)
        $images = BlogImage::all();
        if ($images->isNotEmpty()) {
            $blogPosts->each(function ($post) use ($images) {
                $randomImages = $images->random(rand(0, min(3, $images->count())));
                $randomImages->each(function ($image, $index) use ($post) {
                    $post->images()->attach($image->id, [
                        'sort_order' => $index + 1,
                        'caption' => 'Image caption for ' . $image->title
                    ]);
                });
            });
        }

        // Attach videos to posts (random 0-2 videos per post)
        $videos = BlogVideo::all();
        if ($videos->isNotEmpty()) {
            $blogPosts->each(function ($post) use ($videos) {
                $randomVideos = $videos->random(rand(0, min(2, $videos->count())));
                $randomVideos->each(function ($video, $index) use ($post) {
                    $post->videos()->attach($video->id, [
                        'sort_order' => $index + 1,
                        'caption' => 'Video caption for ' . $video->title
                    ]);
                });
            });
        }

        $this->command->info('Created ' . $blogPosts->count() . ' blog posts with relationships.');
    }

    /**
     * Ensure we have related data for blog posts
     */
    private function ensureRelatedData(): void
    {
        // Ensure we have at least one admin user
        if (User::where('is_admin', true)->count() === 0) {
            User::factory()->create(['is_admin' => true]);
        }

        // Ensure we have some categories
        if (BlogCategory::count() === 0) {
            BlogCategory::factory()->count(5)->create();
        }

        // Ensure we have some subcategories
        if (BlogSubcategory::count() === 0) {
            BlogSubcategory::factory()->count(10)->create();
        }

        // Ensure we have some tags
        if (BlogTag::count() === 0) {
            BlogTag::factory()->count(15)->create();
        }

        // Ensure we have some images
        if (BlogImage::count() === 0) {
            BlogImage::factory()->count(20)->create();
        }

        // Ensure we have some videos
        if (BlogVideo::count() === 0) {
            BlogVideo::factory()->count(10)->create();
        }
    }
}
