<?php

namespace Tests\Feature\Admin;

use App\Models\BlogCategory;
use App\Models\BlogImage;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    protected function createPostData(array $overrides = []): array
    {
        $category = $overrides['blog_category_id'] ?? BlogCategory::factory()->create();
        if (isset($overrides['blog_category_id'])) {
            unset($overrides['blog_category_id']);
        }

        return array_merge([
            'title' => 'Test Blog Post',
            'excerpt' => 'Test excerpt for the blog post',
            'content' => 'This is the full content of the test blog post.',
            'language' => 'en',
            'status' => 'draft',
            'blog_category_id' => is_object($category) ? $category->id : $category,
        ], $overrides);
    }

    public function test_admin_can_view_blog_posts_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog-posts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-posts.index');
        $response->assertViewHas('blogPosts');
        $response->assertViewHas('categories');
    }

    public function test_non_admin_cannot_access_blog_posts_index(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.blog-posts.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_blog_posts_index(): void
    {
        $response = $this->get(route('admin.blog-posts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog-posts.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-posts.create');
        $response->assertViewHas('categories');
        $response->assertViewHas('subcategories');
        $response->assertViewHas('tags');
        $response->assertViewHas('images');
        $response->assertViewHas('videos');
    }

    public function test_admin_can_store_blog_post(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
        ]);

        $response = $this->post(route('admin.blog-posts.store'), $data);

        $response->assertRedirect(route('admin.blog-posts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Test Blog Post',
            'excerpt' => 'Test excerpt for the blog post',
        ]);
    }

    public function test_store_sets_user_id(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
        ]);

        $this->post(route('admin.blog-posts.store'), $data);

        $post = BlogPost::first();
        $this->assertEquals($this->admin->id, $post->user_id);
    }

    public function test_store_sets_published_at_when_published(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();
        $data = [
            'title' => 'Published Post Test',
            'excerpt' => 'Test excerpt',
            'content' => 'Test content for the post',
            'language' => 'en',
            'status' => 'published',
            'blog_category_id' => $category->id,
        ];

        $response = $this->post(route('admin.blog-posts.store'), $data);

        $response->assertStatus(302);

        $post = BlogPost::where('title', 'Published Post Test')->first();
        $this->assertNotNull($post, 'Blog post was not created');
        $this->assertNotNull($post->published_at);
    }

    public function test_store_syncs_tags(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();
        $tag1 = BlogTag::factory()->create();
        $tag2 = BlogTag::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'tags' => [$tag1->id, $tag2->id],
        ]);

        $this->post(route('admin.blog-posts.store'), $data);

        $post = BlogPost::first();
        $this->assertCount(2, $post->tags);
        $this->assertTrue($post->tags->contains($tag1));
        $this->assertTrue($post->tags->contains($tag2));
    }

    public function test_store_syncs_images(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();
        $image1 = BlogImage::factory()->create();
        $image2 = BlogImage::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'images' => [$image1->id, $image2->id],
        ]);

        $this->post(route('admin.blog-posts.store'), $data);

        $post = BlogPost::first();
        $this->assertCount(2, $post->images);
        $this->assertTrue($post->images->contains($image1));
        $this->assertTrue($post->images->contains($image2));
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.blog-posts.store'), []);

        $response->assertSessionHasErrors(['title', 'content', 'language', 'status', 'blog_category_id']);
    }

    public function test_store_validates_language(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'language' => 'invalid',
        ]);

        $response = $this->post(route('admin.blog-posts.store'), $data);

        $response->assertSessionHasErrors(['language']);
    }

    public function test_store_validates_status(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'status' => 'invalid',
        ]);

        $response = $this->post(route('admin.blog-posts.store'), $data);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_store_validates_category_exists(): void
    {
        $this->actingAs($this->admin);

        $data = $this->createPostData([
            'blog_category_id' => 99999,
        ]);

        $response = $this->post(route('admin.blog-posts.store'), $data);

        $response->assertSessionHasErrors(['blog_category_id']);
    }

    public function test_admin_can_view_blog_post(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();

        $response = $this->get(route('admin.blog-posts.show', $post));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-posts.show');
        $response->assertViewHas('blogPost', $post);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();

        $response = $this->get(route('admin.blog-posts.edit', $post));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-posts.edit');
        $response->assertViewHas('blogPost');
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
    }

    public function test_admin_can_update_blog_post(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();
        $post = BlogPost::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'title' => 'Updated Title',
        ]);

        $response = $this->put(route('admin.blog-posts.update', $post), $data);

        $response->assertRedirect(route('admin.blog-posts.index'));
        $response->assertSessionHas('success');

        $post->refresh();
        $this->assertEquals('Updated Title', $post->title);
    }

    public function test_update_sets_published_at_when_changing_to_published(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();
        $post = BlogPost::factory()->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'status' => 'published',
        ]);

        $this->put(route('admin.blog-posts.update', $post), $data);

        $post->refresh();
        $this->assertNotNull($post->published_at);
    }

    public function test_update_does_not_change_published_at_when_already_published(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();
        $originalDate = now()->subDays(5);
        $post = BlogPost::factory()->create([
            'status' => 'published',
            'published_at' => $originalDate,
        ]);

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'status' => 'published',
        ]);

        $this->put(route('admin.blog-posts.update', $post), $data);

        $post->refresh();
        $this->assertEquals($originalDate->toDateTimeString(), $post->published_at->toDateTimeString());
    }

    public function test_update_syncs_relationships(): void
    {
        $this->actingAs($this->admin);

        $category = BlogCategory::factory()->create();
        $post = BlogPost::factory()->create();
        $tag1 = BlogTag::factory()->create();
        $tag2 = BlogTag::factory()->create();

        $data = $this->createPostData([
            'blog_category_id' => $category->id,
            'tags' => [$tag1->id, $tag2->id],
        ]);

        $this->put(route('admin.blog-posts.update', $post), $data);

        $post->refresh();
        $this->assertCount(2, $post->tags);
    }

    public function test_admin_can_delete_blog_post(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();

        $response = $this->delete(route('admin.blog-posts.destroy', $post));

        $response->assertRedirect(route('admin.blog-posts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('blog_posts', ['id' => $post->id]);
    }

    public function test_delete_detaches_relationships(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();
        $tag = BlogTag::factory()->create();
        $image = BlogImage::factory()->create();

        $post->tags()->attach($tag);
        $post->images()->attach($image);

        $this->delete(route('admin.blog-posts.destroy', $post));

        $this->assertCount(0, $post->tags);
        $this->assertCount(0, $post->images);
    }

    public function test_admin_can_toggle_featured(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create(['is_featured' => false]);

        $response = $this->patch(route('admin.blog-posts.toggle-featured', $post));

        $response->assertSessionHas('success');

        $post->refresh();
        $this->assertTrue($post->is_featured);
    }

    public function test_admin_can_toggle_featured_off(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create(['is_featured' => true]);

        $response = $this->patch(route('admin.blog-posts.toggle-featured', $post));

        $response->assertSessionHas('success');

        $post->refresh();
        $this->assertFalse($post->is_featured);
    }

    public function test_admin_can_duplicate_post(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create([
            'title' => 'Original Post',
            'status' => 'published',
        ]);

        $response = $this->post(route('admin.blog-posts.duplicate', $post));

        $response->assertRedirect(route('admin.blog-posts.edit', BlogPost::orderBy('id', 'desc')->first()));
        $response->assertSessionHas('success');

        $duplicatedPost = BlogPost::orderBy('id', 'desc')->first();
        $this->assertEquals('Original Post (Copy)', $duplicatedPost->title);
        $this->assertEquals('draft', $duplicatedPost->status);
        $this->assertNull($duplicatedPost->published_at);
        $this->assertEquals(0, $duplicatedPost->views_count);
    }

    public function test_duplicate_copies_tags(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();
        $tag = BlogTag::factory()->create();
        $post->tags()->attach($tag);

        $this->post(route('admin.blog-posts.duplicate', $post));

        $duplicatedPost = BlogPost::orderBy('id', 'desc')->first();
        $this->assertCount(1, $duplicatedPost->tags);
        $this->assertTrue($duplicatedPost->tags->contains($tag));
    }

    public function test_duplicate_copies_images(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();
        $image = BlogImage::factory()->create();
        $post->images()->attach($image);

        $this->post(route('admin.blog-posts.duplicate', $post));

        $duplicatedPost = BlogPost::orderBy('id', 'desc')->first();
        $this->assertCount(1, $duplicatedPost->images);
        $this->assertTrue($duplicatedPost->images->contains($image));
    }

    public function test_duplicate_sets_current_user_as_author(): void
    {
        $this->actingAs($this->admin);

        $originalAuthor = User::factory()->create();
        $post = BlogPost::factory()->create(['user_id' => $originalAuthor->id]);

        $this->post(route('admin.blog-posts.duplicate', $post));

        $duplicatedPost = BlogPost::orderBy('id', 'desc')->first();
        $this->assertEquals($this->admin->id, $duplicatedPost->user_id);
    }

    public function test_index_pagination(): void
    {
        $this->actingAs($this->admin);

        BlogPost::factory()->count(20)->create();

        $response = $this->get(route('admin.blog-posts.index'));

        $response->assertViewHas('blogPosts');
        $this->assertCount(15, $response->viewData('blogPosts')); // Default pagination is 15
    }

    public function test_index_filters_by_status(): void
    {
        $this->actingAs($this->admin);

        BlogPost::factory()->create(['status' => 'published']);
        BlogPost::factory()->create(['status' => 'draft']);

        $response = $this->get(route('admin.blog-posts.index', ['status' => 'draft']));

        $response->assertViewHas('blogPosts');
        $this->assertCount(1, $response->viewData('blogPosts'));
    }

    public function test_index_filters_by_category(): void
    {
        $this->actingAs($this->admin);

        $category1 = BlogCategory::factory()->create();
        $category2 = BlogCategory::factory()->create();

        BlogPost::factory()->create(['blog_category_id' => $category1->id]);
        BlogPost::factory()->create(['blog_category_id' => $category2->id]);

        $response = $this->get(route('admin.blog-posts.index', ['category_id' => $category1->id]));

        $response->assertViewHas('blogPosts');
        $this->assertCount(1, $response->viewData('blogPosts'));
    }

    public function test_index_filters_by_language(): void
    {
        $this->actingAs($this->admin);

        BlogPost::factory()->create(['language' => 'en']);
        BlogPost::factory()->create(['language' => 'pt']);

        $response = $this->get(route('admin.blog-posts.index', ['language' => 'en']));

        $response->assertViewHas('blogPosts');
        $this->assertCount(1, $response->viewData('blogPosts'));
    }

    public function test_index_search_by_title(): void
    {
        $this->actingAs($this->admin);

        BlogPost::factory()->create(['title' => 'Unique Searchable Title']);
        BlogPost::factory()->create(['title' => 'Other Post']);

        $response = $this->get(route('admin.blog-posts.index', ['search' => 'Unique']));

        $response->assertViewHas('blogPosts');
        $this->assertCount(1, $response->viewData('blogPosts'));
    }

    public function test_index_search_by_content(): void
    {
        $this->actingAs($this->admin);

        BlogPost::factory()->create(['content' => 'This has unique searchable content']);
        BlogPost::factory()->create(['content' => 'Different content here']);

        $response = $this->get(route('admin.blog-posts.index', ['search' => 'unique searchable']));

        $response->assertViewHas('blogPosts');
        $this->assertCount(1, $response->viewData('blogPosts'));
    }

    public function test_index_search_by_excerpt(): void
    {
        $this->actingAs($this->admin);

        BlogPost::factory()->create(['excerpt' => 'Unique searchable excerpt']);
        BlogPost::factory()->create(['excerpt' => 'Other excerpt']);

        $response = $this->get(route('admin.blog-posts.index', ['search' => 'Unique searchable']));

        $response->assertViewHas('blogPosts');
        $this->assertCount(1, $response->viewData('blogPosts'));
    }
}
