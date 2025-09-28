<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BlogImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class BlogImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a blog image can be created with valid data.
     */
    public function test_blog_image_can_be_created(): void
    {
        $blogImage = BlogImage::factory()->create([
            'title' => 'Test Image',
            'language' => 'pt'
        ]);

        $this->assertInstanceOf(BlogImage::class, $blogImage);
        $this->assertEquals('Test Image', $blogImage->title);
        $this->assertEquals('pt', $blogImage->language);
        $this->assertDatabaseHas('blog_images', [
            'title' => 'Test Image',
            'language' => 'pt'
        ]);
    }

    /**
     * Test that slug is automatically generated from title.
     */
    public function test_slug_is_generated_from_title(): void
    {
        $title = 'Test Image Title';
        $expectedSlug = Str::slug($title);
        
        $blogImage = new BlogImage([
            'title' => $title,
            'language' => 'pt',
            'filename' => 'test.jpg',
            'original_filename' => 'test.jpg',
            'path' => 'blog/images/test.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
            'width' => 800,
            'height' => 600,
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        $blogImage->save();

        $this->assertEquals($expectedSlug, $blogImage->slug);
    }

    /**
     * Test that alt_text defaults to title if not provided.
     */
    public function test_alt_text_defaults_to_title(): void
    {
        $title = 'Test Image';
        
        $blogImage = BlogImage::factory()->create([
            'title' => $title,
            'alt_text' => null
        ]);

        $this->assertEquals($title, $blogImage->alt_text);
    }

    /**
     * Test that custom alt_text is preserved when provided.
     */
    public function test_custom_alt_text_is_preserved(): void
    {
        $title = 'Test Image';
        $altText = 'Custom Alt Text';
        
        $blogImage = BlogImage::factory()->create([
            'title' => $title,
            'alt_text' => $altText
        ]);

        $this->assertEquals($altText, $blogImage->alt_text);
    }

    /**
     * Test the generateSlug static method.
     */
    public function test_generate_slug_method(): void
    {
        $title = 'Test Image Title';
        $expectedSlug = Str::slug($title);
        
        $slug = BlogImage::generateSlug($title);
        
        $this->assertEquals($expectedSlug, $slug);
    }

    /**
     * Test that generateSlug handles duplicate slugs.
     */
    public function test_generate_slug_handles_duplicates(): void
    {
        $title = 'Test Image';
        
        // Create first image with this title
        BlogImage::factory()->create([
            'title' => $title,
            'slug' => Str::slug($title)
        ]);
        
        // Generate slug for second image with same title
        $newSlug = BlogImage::generateSlug($title);
        
        // Should be different from the original slug
        $this->assertNotEquals(Str::slug($title), $newSlug);
        $this->assertStringContainsString(Str::slug($title), $newSlug);
    }

    /**
     * Test the getUrl method.
     */
    public function test_get_url_method(): void
    {
        $blogImage = BlogImage::factory()->create([
            'path' => 'blog/images/test-image.jpg'
        ]);

        $expectedUrl = config('blog.images.url_prefix') . '/blog/images/test-image.jpg';
        
        $this->assertEquals($expectedUrl, $blogImage->getUrl());
    }

    /**
     * Test the scope methods.
     */
    public function test_active_scope(): void
    {
        BlogImage::factory()->create(['is_active' => true]);
        BlogImage::factory()->create(['is_active' => false]);

        $activeImages = BlogImage::active()->get();
        
        $this->assertCount(1, $activeImages);
        $this->assertTrue($activeImages->first()->is_active);
    }

    public function test_language_scope(): void
    {
        BlogImage::factory()->create(['language' => 'pt']);
        BlogImage::factory()->create(['language' => 'en']);
        BlogImage::factory()->create(['language' => 'pt']);

        $ptImages = BlogImage::language('pt')->get();
        
        $this->assertCount(2, $ptImages);
        $ptImages->each(function ($image) {
            $this->assertEquals('pt', $image->language);
        });
    }

    /**
     * Test fillable attributes.
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'title', 'slug', 'description', 'alt_text', 'filename', 'original_filename',
            'path', 'mime_type', 'file_size', 'width', 'height', 'language',
            'is_active', 'sort_order'
        ];

        $blogImage = new BlogImage();
        
        $this->assertEquals($fillable, $blogImage->getFillable());
    }

    /**
     * Test casts.
     */
    public function test_casts(): void
    {
        $blogImage = BlogImage::factory()->create([
            'is_active' => true,
            'file_size' => 1024,
            'width' => 800,
            'height' => 600,
            'sort_order' => 1
        ]);

        $this->assertIsBool($blogImage->is_active);
        $this->assertIsInt($blogImage->file_size);
        $this->assertIsInt($blogImage->width);
        $this->assertIsInt($blogImage->height);
        $this->assertIsInt($blogImage->sort_order);
    }
}
