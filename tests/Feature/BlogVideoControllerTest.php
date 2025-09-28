<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BlogVideo;
use App\Models\User;

class BlogVideoControllerTest extends TestCase
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
        Storage::fake(config('blog.videos.disk'));
    }

    /**
     * Test that admin can view the blog videos index page.
     */
    public function test_admin_can_view_blog_videos_index(): void
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('admin.blog-videos.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.videos.index');
    }

    /**
     * Test that non-admin users cannot access blog videos.
     */
    public function test_non_admin_cannot_access_blog_videos(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);
        
        $response = $this->get(route('admin.blog-videos.index'));
        
        $response->assertStatus(403);
    }

    /**
     * Test that admin can view the create form.
     */
    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin);
        
        $response = $this->get(route('admin.blog-videos.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.videos.create');
    }

    /**
     * Test that admin can store a new blog video.
     */
    public function test_admin_can_store_blog_video(): void
    {
        $this->actingAs($this->admin);
        
        $videoFile = UploadedFile::fake()->create('test-video.mp4', 10240, 'video/mp4');
        $thumbnailFile = UploadedFile::fake()->image('test-thumbnail.jpg', 800, 600);
        
        $data = [
            'title' => 'Test Video',
            'description' => 'Test video description',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 1,
            'video' => $videoFile,
            'thumbnail' => $thumbnailFile
        ];
        
        $response = $this->post(route('admin.blog-videos.store'), $data);
        
        $response->assertRedirect(route('admin.blog-videos.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('blog_videos', [
            'title' => 'Test Video',
            'description' => 'Test video description',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 1
        ]);
        
        // Check that files were stored
        $blogVideo = BlogVideo::where('title', 'Test Video')->first();
        $this->assertTrue(Storage::disk(config('blog.videos.disk'))->exists($blogVideo->path));
        $this->assertTrue(Storage::disk(config('blog.videos.disk'))->exists($blogVideo->thumbnail_path));
    }

    /**
     * Test validation errors when storing blog video.
     */
    public function test_store_validation_errors(): void
    {
        $this->actingAs($this->admin);
        
        $response = $this->post(route('admin.blog-videos.store'), []);
        
        $response->assertSessionHasErrors(['title', 'language']);
    }

    /**
     * Test that admin can view a specific blog video.
     */
    public function test_admin_can_view_blog_video(): void
    {
        $this->actingAs($this->admin);
        
        $blogVideo = BlogVideo::factory()->create();
        
        $response = $this->get(route('admin.blog-videos.show', $blogVideo));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.videos.show');
        $response->assertViewHas('video', $blogVideo);
    }

    /**
     * Test that admin can view the edit form.
     */
    public function test_admin_can_view_edit_form(): void
    {
        $this->actingAs($this->admin);
        
        $blogVideo = BlogVideo::factory()->create();
        
        $response = $this->get(route('admin.blog-videos.edit', $blogVideo));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.videos.edit');
        $response->assertViewHas('video', $blogVideo);
    }

    /**
     * Test that admin can update a blog video.
     */
    public function test_admin_can_update_blog_video(): void
    {
        $this->actingAs($this->admin);
        
        $blogVideo = BlogVideo::factory()->create([
            'title' => 'Original Title',
            'description' => 'Original description'
        ]);
        
        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'language' => 'en',
            'is_active' => false,
            'sort_order' => 2
        ];
        
        $response = $this->put(route('admin.blog-videos.update', $blogVideo), $data);
        
        $response->assertRedirect(route('admin.blog-videos.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('blog_videos', [
            'id' => $blogVideo->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'language' => 'en',
            'is_active' => false,
            'sort_order' => 2
        ]);
    }

    /**
     * Test that admin can update blog video with new files.
     */
    public function test_admin_can_update_blog_video_with_new_files(): void
    {
        $this->actingAs($this->admin);
        
        // Create initial video
        $oldVideoFile = UploadedFile::fake()->create('old-video.mp4', 5120, 'video/mp4');
        $oldThumbnailFile = UploadedFile::fake()->image('old-thumbnail.jpg');
        $blogVideo = BlogVideo::factory()->create();
        
        // Store old files
        $oldVideoPath = $oldVideoFile->storeAs(config('blog.videos.path'), 'old-video.mp4', config('blog.videos.disk'));
        $oldThumbnailPath = $oldThumbnailFile->storeAs(config('blog.videos.thumbnail_path'), 'old-thumbnail.jpg', config('blog.videos.disk'));
        $blogVideo->update([
            'path' => $oldVideoPath,
            'thumbnail_path' => $oldThumbnailPath
        ]);
        
        // Update with new files
        $newVideoFile = UploadedFile::fake()->create('new-video.mp4', 8192, 'video/mp4');
        $newThumbnailFile = UploadedFile::fake()->image('new-thumbnail.jpg', 1000, 800);
        
        $data = [
            'title' => 'Updated Title',
            'language' => 'pt',
            'video' => $newVideoFile,
            'thumbnail' => $newThumbnailFile
        ];
        
        $response = $this->put(route('admin.blog-videos.update', $blogVideo), $data);
        
        $response->assertRedirect(route('admin.blog-videos.index'));
        
        // Check old files were deleted and new files exist
        $this->assertFalse(Storage::disk(config('blog.videos.disk'))->exists($oldVideoPath));
        $this->assertFalse(Storage::disk(config('blog.videos.disk'))->exists($oldThumbnailPath));
        
        $blogVideo->refresh();
        $this->assertTrue(Storage::disk(config('blog.videos.disk'))->exists($blogVideo->path));
        $this->assertTrue(Storage::disk(config('blog.videos.disk'))->exists($blogVideo->thumbnail_path));
    }

    /**
     * Test that admin can delete a blog video.
     */
    public function test_admin_can_delete_blog_video(): void
    {
        $this->actingAs($this->admin);
        
        $videoFile = UploadedFile::fake()->create('test-video.mp4', 5120, 'video/mp4');
        $thumbnailFile = UploadedFile::fake()->image('test-thumbnail.jpg');
        
        $videoPath = $videoFile->storeAs(config('blog.videos.path'), 'test-video.mp4', config('blog.videos.disk'));
        $thumbnailPath = $thumbnailFile->storeAs(config('blog.videos.thumbnail_path'), 'test-thumbnail.jpg', config('blog.videos.disk'));
        
        $blogVideo = BlogVideo::factory()->create([
            'path' => $videoPath,
            'thumbnail_path' => $thumbnailPath
        ]);
        
        $response = $this->delete(route('admin.blog-videos.destroy', $blogVideo));
        
        $response->assertRedirect(route('admin.blog-videos.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('blog_videos', ['id' => $blogVideo->id]);
        $this->assertFalse(Storage::disk(config('blog.videos.disk'))->exists($videoPath));
        $this->assertFalse(Storage::disk(config('blog.videos.disk'))->exists($thumbnailPath));
    }

    /**
     * Test pagination on index page.
     */
    public function test_index_pagination(): void
    {
        $this->actingAs($this->admin);
        
        // Create more videos than the pagination limit
        BlogVideo::factory()->count(25)->create();
        
        $response = $this->get(route('admin.blog-videos.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('videos');
    }

    /**
     * Test filtering by language.
     */
    public function test_index_language_filter(): void
    {
        $this->actingAs($this->admin);
        
        BlogVideo::factory()->create(['language' => 'pt']);
        BlogVideo::factory()->create(['language' => 'en']);
        
        $response = $this->get(route('admin.blog-videos.index', ['language' => 'pt']));
        
        $response->assertStatus(200);
        $response->assertViewHas('videos');
    }

    /**
     * Test search functionality.
     */
    public function test_index_search(): void
    {
        $this->actingAs($this->admin);
        
        BlogVideo::factory()->create(['title' => 'Searchable Video']);
        BlogVideo::factory()->create(['title' => 'Other Video']);
        
        $response = $this->get(route('admin.blog-videos.index', ['search' => 'Searchable']));
        
        $response->assertStatus(200);
        $response->assertViewHas('videos');
    }

    /**
     * Test filtering by status.
     */
    public function test_index_status_filter(): void
    {
        $this->actingAs($this->admin);
        
        BlogVideo::factory()->create(['is_active' => true]);
        BlogVideo::factory()->create(['is_active' => false]);
        
        $response = $this->get(route('admin.blog-videos.index', ['status' => 'active']));
        
        $response->assertStatus(200);
        $response->assertViewHas('videos');
    }
}