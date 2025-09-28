<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BlogImage;
use App\Models\User;

class BlogImageControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user for testing
        $this->admin = User::factory()->create([
            'is_admin' => true
        ]);
        
        // Fake the storage disk
        Storage::fake(config('blog.images.disk'));
    }

    /**
     * Test that admin can view the blog images index page.
     */
    public function test_admin_can_view_blog_images_index(): void
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('admin.blog-images.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.images.index');
    }

    /**
     * Test that non-admin users cannot access blog images.
     */
    public function test_non_admin_cannot_access_blog_images(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);
        
        $response = $this->get(route('admin.blog-images.index'));
        
        $response->assertStatus(403);
    }

    /**
     * Test that admin can view the create form.
     */
    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('admin.blog-images.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.images.create');
    }

    /**
     * Test that admin can store a new blog image.
     */
    public function test_admin_can_store_blog_image(): void
    {
        $this->actingAs($this->admin);
        
        $file = UploadedFile::fake()->image('test-image.jpg', 800, 600);
        
        $data = [
            'title' => 'Test Image',
            'description' => 'Test description',
            'alt_text' => 'Test Image Alt Text',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 1,
            'image' => $file
        ];
        
        $response = $this->post(route('admin.blog-images.store'), $data);
        
        $response->assertRedirect(route('admin.blog-images.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('blog_images', [
            'title' => 'Test Image',
            'description' => 'Test description',
            'alt_text' => 'Test Image Alt Text',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Check that file was stored
        $blogImage = BlogImage::where('title', 'Test Image')->first();
        $this->assertTrue(Storage::disk(config('blog.images.disk'))->exists($blogImage->path));
    }

    /**
     * Test validation errors when storing blog image.
     */
    public function test_store_validation_errors(): void
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.blog-images.store'), []);
        
        $response->assertSessionHasErrors(['title', 'alt_text', 'language']);
    }

    /**
     * Test that admin can view a specific blog image.
     */
    public function test_admin_can_view_blog_image(): void
    {
        $this->actingAs($this->admin);
        
        $blogImage = BlogImage::factory()->create();
        
        $response = $this->get(route('admin.blog-images.show', $blogImage));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.images.show');
        $response->assertViewHas('image', $blogImage);
    }

    /**
     * Test that admin can view the edit form.
     */
    public function test_admin_can_view_edit_form(): void
    {
        $this->actingAs($this->admin);
        
        $blogImage = BlogImage::factory()->create();
        
        $response = $this->get(route('admin.blog-images.edit', $blogImage));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.images.edit');
        $response->assertViewHas('image', $blogImage);
    }

    /**
     * Test that admin can update a blog image.
     */
    public function test_admin_can_update_blog_image(): void
    {
        $this->actingAs($this->admin);
        
        $blogImage = BlogImage::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original description'
        ]);
        
        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'alt_text' => 'Updated alt text',
            'language' => 'en',
            'is_active' => false,
            'sort_order' => 2
        ];
        
        $response = $this->put(route('admin.blog-images.update', $blogImage), $data);
        
        $response->assertRedirect(route('admin.blog-images.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('blog_images', [
            'id' => $blogImage->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'alt_text' => 'Updated alt text',
            'language' => 'en',
            'is_active' => false,
            'sort_order' => 2
        ]);
    }

    /**
     * Test that admin can update blog image with new file.
     */
    public function test_admin_can_update_blog_image_with_new_file(): void
    {
        $this->actingAs($this->admin);
        
        // Create initial image
        $oldFile = UploadedFile::fake()->image('old-image.jpg');
        $blogImage = BlogImage::factory()->create();
        
        // Store old file
        $oldPath = $oldFile->storeAs(config('blog.images.path'), 'old-image.jpg', config('blog.images.disk'));
        $blogImage->update(['path' => $oldPath]);
        
        // Update with new file
        $newFile = UploadedFile::fake()->image('new-image.jpg', 1000, 800);
        
        $data = [
            'title' => 'Updated Title',
            'alt_text' => 'Updated Image Alt Text',
            'language' => 'pt',
            'image' => $newFile
        ];
        
        $response = $this->put(route('admin.blog-images.update', $blogImage), $data);
        
        $response->assertRedirect(route('admin.blog-images.index'));
        
        // Check old file was deleted and new file exists
        $this->assertFalse(Storage::disk(config('blog.images.disk'))->exists($oldPath));
        
        $blogImage->refresh();
        $this->assertTrue(Storage::disk(config('blog.images.disk'))->exists($blogImage->path));
    }

    /**
     * Test that admin can delete a blog image.
     */
    public function test_admin_can_delete_blog_image(): void
    {
        $this->actingAs($this->admin);
        
        $file = UploadedFile::fake()->image('test-image.jpg');
        $path = $file->storeAs(config('blog.images.path'), 'test-image.jpg', config('blog.images.disk'));
        
        $blogImage = BlogImage::factory()->create(['path' => $path]);
        
        $response = $this->delete(route('admin.blog-images.destroy', $blogImage));
        
        $response->assertRedirect(route('admin.blog-images.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('blog_images', ['id' => $blogImage->id]);
        $this->assertFalse(Storage::disk(config('blog.images.disk'))->exists($path));
    }

    /**
     * Test pagination on index page.
     */
    public function test_index_pagination(): void
    {
        $this->actingAs($this->admin);
        
        // Create more images than the pagination limit
        BlogImage::factory()->count(25)->create();
        
        $response = $this->get(route('admin.blog-images.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('images');
    }

    /**
     * Test filtering by language.
     */
    public function test_index_language_filter(): void
    {
        $this->actingAs($this->admin);
        
        BlogImage::factory()->create(['language' => 'pt']);
        BlogImage::factory()->create(['language' => 'en']);
        
        $response = $this->get(route('admin.blog-images.index', ['language' => 'pt']));
        
        $response->assertStatus(200);
        $response->assertViewHas('images');
    }

    /**
     * Test search functionality.
     */
    public function test_index_search(): void
    {
        $this->actingAs($this->admin);
        
        BlogImage::factory()->create(['title' => 'Searchable Image']);
        BlogImage::factory()->create(['title' => 'Other Image']);
        
        $response = $this->get(route('admin.blog-images.index', ['search' => 'Searchable']));
        
        $response->assertStatus(200);
        $response->assertViewHas('images');
    }
}
