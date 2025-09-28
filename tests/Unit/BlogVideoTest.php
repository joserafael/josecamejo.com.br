<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BlogVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class BlogVideoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a blog video can be created with valid data.
     */
    public function test_blog_video_can_be_created(): void
    {
        $blogVideo = BlogVideo::factory()->create([
            'title' => 'Test Video',
            'language' => 'pt'
        ]);

        $this->assertInstanceOf(BlogVideo::class, $blogVideo);
        $this->assertEquals('Test Video', $blogVideo->title);
        $this->assertEquals('pt', $blogVideo->language);
        $this->assertDatabaseHas('blog_videos', [
            'title' => 'Test Video',
            'language' => 'pt'
        ]);
    }

    /**
     * Test that slug is automatically generated from title.
     */
    public function test_slug_is_generated_from_title(): void
    {
        $title = 'Test Video Title';
        $expectedSlug = Str::slug($title);
        
        $blogVideo = new BlogVideo([
            'title' => $title,
            'language' => 'pt',
            'filename' => 'test.mp4',
            'original_filename' => 'test.mp4',
            'path' => 'blog/videos/test.mp4',
            'mime_type' => 'video/mp4',
            'file_size' => 10240,
            'width' => 1920,
            'height' => 1080,
            'duration' => 120,
            'thumbnail_filename' => 'test-thumb.jpg',
            'thumbnail_path' => 'blog/videos/thumbnails/test-thumb.jpg',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $blogVideo->save();

        $this->assertEquals($expectedSlug, $blogVideo->slug);
    }

    /**
     * Test the generateSlug static method.
     */
    public function test_generate_slug_method(): void
    {
        $title = 'Test Video Title';
        $expectedSlug = Str::slug($title);
        
        $slug = BlogVideo::generateSlug($title);
        
        $this->assertEquals($expectedSlug, $slug);
    }

    /**
     * Test that generateSlug handles duplicate slugs.
     */
    public function test_generate_slug_handles_duplicates(): void
    {
        $title = 'Test Video';
        
        // Create first video with this title
        BlogVideo::factory()->create([
            'title' => $title,
            'slug' => Str::slug($title)
        ]);
        
        // Generate slug for second video with same title
        $newSlug = BlogVideo::generateSlug($title);
        
        // Should be different from the original slug
        $this->assertNotEquals(Str::slug($title), $newSlug);
        $this->assertStringContainsString(Str::slug($title), $newSlug);
    }

    /**
     * Test the getUrl method.
     */
    public function test_get_url_method(): void
    {
        $blogVideo = BlogVideo::factory()->create([
            'path' => 'blog/videos/test-video.mp4'
        ]);

        $expectedUrl = config('blog.videos.url_prefix') . '/blog/videos/test-video.mp4';
        
        $this->assertEquals($expectedUrl, $blogVideo->getUrl());
    }

    /**
     * Test the getThumbnailUrl method.
     */
    public function test_get_thumbnail_url_method(): void
    {
        $blogVideo = BlogVideo::factory()->create([
            'thumbnail_path' => 'blog/videos/thumbnails/test-thumbnail.jpg'
        ]);

        $expectedUrl = config('blog.videos.url_prefix') . '/blog/videos/thumbnails/test-thumbnail.jpg';
        
        $this->assertEquals($expectedUrl, $blogVideo->getThumbnailUrl());
    }

    /**
     * Test the scope methods.
     */
    public function test_active_scope(): void
    {
        BlogVideo::factory()->create(['is_active' => true]);
        BlogVideo::factory()->create(['is_active' => false]);

        $activeVideos = BlogVideo::active()->get();
        
        $this->assertCount(1, $activeVideos);
        $this->assertTrue($activeVideos->first()->is_active);
    }

    public function test_language_scope(): void
    {
        BlogVideo::factory()->create(['language' => 'pt']);
        BlogVideo::factory()->create(['language' => 'en']);
        BlogVideo::factory()->create(['language' => 'pt']);

        $ptVideos = BlogVideo::language('pt')->get();
        
        $this->assertCount(2, $ptVideos);
        $ptVideos->each(function ($video) {
            $this->assertEquals('pt', $video->language);
        });
    }

    /**
     * Test fillable attributes.
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'title', 'slug', 'description', 'filename', 'original_filename',
            'path', 'mime_type', 'file_size', 'width', 'height', 'duration',
            'thumbnail_path', 'language', 'is_active', 'sort_order'
        ];

        $blogVideo = new BlogVideo();
        
        $this->assertEquals($fillable, $blogVideo->getFillable());
    }

    /**
     * Test casts.
     */
    public function test_casts(): void
    {
        $blogVideo = BlogVideo::factory()->create([
            'is_active' => true,
            'file_size' => 10240,
            'width' => 1920,
            'height' => 1080,
            'duration' => 120,
            'sort_order' => 1
        ]);

        $this->assertIsBool($blogVideo->is_active);
        $this->assertIsInt($blogVideo->file_size);
        $this->assertIsInt($blogVideo->width);
        $this->assertIsInt($blogVideo->height);
        $this->assertIsInt($blogVideo->duration);
        $this->assertIsInt($blogVideo->sort_order);
    }

    /**
     * Test duration formatting.
     */
    public function test_duration_formatting(): void
    {
        // Test seconds only
        $video1 = BlogVideo::factory()->create(['duration' => 45]);
        $this->assertEquals('0:45', $video1->getFormattedDuration());

        // Test minutes and seconds
        $video2 = BlogVideo::factory()->create(['duration' => 125]);
        $this->assertEquals('2:05', $video2->getFormattedDuration());

        // Test hours, minutes and seconds
        $video3 = BlogVideo::factory()->create(['duration' => 3665]);
        $this->assertEquals('1:01:05', $video3->getFormattedDuration());
    }

    /**
     * Test file size formatting.
     */
    public function test_file_size_formatting(): void
    {
        // Test bytes
        $video1 = BlogVideo::factory()->create(['file_size' => 512]);
        $this->assertEquals('512 B', $video1->getFormattedFileSize());

        // Test kilobytes
        $video2 = BlogVideo::factory()->create(['file_size' => 1536]);
        $this->assertEquals('1.5 KB', $video2->getFormattedFileSize());

        // Test megabytes
        $video3 = BlogVideo::factory()->create(['file_size' => 2097152]);
        $this->assertEquals('2 MB', $video3->getFormattedFileSize());

        // Test gigabytes
        $video4 = BlogVideo::factory()->create(['file_size' => 1073741824]);
        $this->assertEquals('1 GB', $video4->getFormattedFileSize());
    }

    /**
     * Test video dimensions formatting.
     */
    public function test_dimensions_formatting(): void
    {
        $blogVideo = BlogVideo::factory()->create([
            'width' => 1920,
            'height' => 1080
        ]);

        $this->assertEquals('1920x1080', $blogVideo->getDimensions());
    }
}