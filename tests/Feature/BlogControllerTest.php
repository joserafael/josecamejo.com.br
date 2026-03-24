<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');
    }

    public function test_blog_index_returns_successful_response(): void
    {
        BlogPost::factory()->published()->create(['language' => 'en']);

        $response = $this->get(route('blog.index', ['locale' => 'en']));

        $response->assertStatus(200);
        $response->assertViewIs('blog.index');
    }

    public function test_blog_index_shows_only_published_posts(): void
    {
        BlogPost::factory()->published()->create(['language' => 'en']);
        BlogPost::factory()->draft()->create(['language' => 'en']);

        $response = $this->get(route('blog.index', ['locale' => 'en']));

        $response->assertViewHas('posts');
        $this->assertCount(1, $response->viewData('posts'));
    }

    public function test_blog_index_shows_only_posts_in_current_language(): void
    {
        BlogPost::factory()->published()->create(['language' => 'en']);
        BlogPost::factory()->published()->create(['language' => 'pt']);

        $response = $this->get(route('blog.index', ['locale' => 'en']));

        $response->assertViewHas('posts');
        $this->assertCount(1, $response->viewData('posts'));
    }

    public function test_blog_index_includes_sidebar_data(): void
    {
        BlogPost::factory()->published()->create(['language' => 'en']);

        $response = $this->get(route('blog.index', ['locale' => 'en']));

        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
        $response->assertViewHas('recentPosts');
    }

    public function test_blog_index_filters_by_category(): void
    {
        $category1 = BlogCategory::factory()->create(['slug' => 'cat-1', 'language' => 'en']);
        $category2 = BlogCategory::factory()->create(['slug' => 'cat-2', 'language' => 'en']);

        BlogPost::factory()->published()->create([
            'blog_category_id' => $category1->id,
            'language' => 'en',
        ]);
        BlogPost::factory()->published()->create([
            'blog_category_id' => $category2->id,
            'language' => 'en',
        ]);

        $response = $this->get(route('blog.index', [
            'locale' => 'en',
            'category' => 'cat-1',
        ]));

        $response->assertViewHas('posts');
        $this->assertCount(1, $response->viewData('posts'));
    }

    public function test_blog_index_filters_by_tag(): void
    {
        $tag1 = BlogTag::factory()->create(['slug' => 'tag-1', 'language' => 'en']);
        $tag2 = BlogTag::factory()->create(['slug' => 'tag-2', 'language' => 'en']);

        $post1 = BlogPost::factory()->published()->create(['language' => 'en']);
        $post2 = BlogPost::factory()->published()->create(['language' => 'en']);

        $post1->tags()->attach($tag1);
        $post2->tags()->attach($tag2);

        $response = $this->get(route('blog.index', [
            'locale' => 'en',
            'tag' => 'tag-1',
        ]));

        $response->assertViewHas('posts');
        $this->assertCount(1, $response->viewData('posts'));
    }

    public function test_blog_index_search_functionality(): void
    {
        BlogPost::factory()->published()->create([
            'title' => 'Unique Searchable Title',
            'language' => 'en',
        ]);
        BlogPost::factory()->published()->create([
            'title' => 'Other Post Title',
            'language' => 'en',
        ]);

        $response = $this->get(route('blog.index', [
            'locale' => 'en',
            'search' => 'Unique',
        ]));

        $response->assertViewHas('posts');
        $this->assertCount(1, $response->viewData('posts'));
    }

    public function test_blog_show_returns_successful_response(): void
    {
        $post = BlogPost::factory()->published()->create(['slug' => 'test-post', 'language' => 'en']);

        $response = $this->get(route('blog.show', ['locale' => 'en', 'slug' => 'test-post']));

        $response->assertStatus(200);
        $response->assertViewIs('blog.show');
    }

    public function test_blog_show_returns_404_for_unpublished_post(): void
    {
        $post = BlogPost::factory()->draft()->create(['slug' => 'draft-post']);

        $response = $this->get(route('blog.show', ['locale' => 'en', 'slug' => 'draft-post']));

        $response->assertStatus(404);
    }

    public function test_blog_show_returns_404_for_wrong_language(): void
    {
        $post = BlogPost::factory()->published()->create([
            'slug' => 'english-post',
            'language' => 'en',
        ]);

        $response = $this->get(route('blog.show', ['locale' => 'pt', 'slug' => 'english-post']));

        $response->assertStatus(404);
    }

    public function test_blog_show_includes_related_posts(): void
    {
        $category = BlogCategory::factory()->create(['language' => 'en']);
        $post1 = BlogPost::factory()->published()->create([
            'blog_category_id' => $category->id,
            'language' => 'en',
        ]);
        $post2 = BlogPost::factory()->published()->create([
            'blog_category_id' => $category->id,
            'language' => 'en',
        ]);

        $response = $this->get(route('blog.show', ['locale' => 'en', 'slug' => $post1->slug]));

        $response->assertViewHas('relatedPosts');
    }

    public function test_blog_category_returns_successful_response(): void
    {
        $category = BlogCategory::factory()->create(['slug' => 'test-category', 'language' => 'en']);

        $response = $this->get(route('blog.category', ['locale' => 'en', 'slug' => 'test-category']));

        $response->assertStatus(200);
        $response->assertViewIs('blog.category');
        $response->assertViewHas('category');
    }

    public function test_blog_category_returns_404_for_invalid_slug(): void
    {
        $response = $this->get(route('blog.category', ['locale' => 'en', 'slug' => 'non-existent']));

        $response->assertStatus(404);
    }

    public function test_blog_category_shows_only_posts_in_category(): void
    {
        $category1 = BlogCategory::factory()->create(['slug' => 'cat-1', 'language' => 'en']);
        $category2 = BlogCategory::factory()->create(['slug' => 'cat-2', 'language' => 'en']);

        BlogPost::factory()->published()->create([
            'blog_category_id' => $category1->id,
            'language' => 'en',
        ]);
        BlogPost::factory()->published()->create([
            'blog_category_id' => $category2->id,
            'language' => 'en',
        ]);

        $response = $this->get(route('blog.category', ['locale' => 'en', 'slug' => 'cat-1']));

        $response->assertViewHas('posts');
        $this->assertCount(1, $response->viewData('posts'));
    }

    public function test_blog_tag_returns_successful_response(): void
    {
        $tag = BlogTag::factory()->create(['slug' => 'test-tag', 'language' => 'en']);

        $response = $this->get(route('blog.tag', ['locale' => 'en', 'slug' => 'test-tag']));

        $response->assertStatus(200);
        $response->assertViewIs('blog.tag');
        $response->assertViewHas('tag');
    }

    public function test_blog_tag_returns_404_for_invalid_slug(): void
    {
        $response = $this->get(route('blog.tag', ['locale' => 'en', 'slug' => 'non-existent']));

        $response->assertStatus(404);
    }

    public function test_blog_tag_shows_only_posts_with_tag(): void
    {
        $tag1 = BlogTag::factory()->create(['slug' => 'tag-1', 'language' => 'en']);
        $tag2 = BlogTag::factory()->create(['slug' => 'tag-2', 'language' => 'en']);

        $post1 = BlogPost::factory()->published()->create(['language' => 'en']);
        $post2 = BlogPost::factory()->published()->create(['language' => 'en']);

        $post1->tags()->attach($tag1);
        $post2->tags()->attach($tag2);

        $response = $this->get(route('blog.tag', ['locale' => 'en', 'slug' => 'tag-1']));

        $response->assertViewHas('posts');
        $this->assertCount(1, $response->viewData('posts'));
    }

    public function test_blog_index_pagination(): void
    {
        BlogPost::factory()->published()->count(15)->create(['language' => 'en']);

        $response = $this->get(route('blog.index', ['locale' => 'en']));

        $response->assertViewHas('posts');
        $this->assertCount(12, $response->viewData('posts')); // Default per page is 12
    }

    public function test_sidebar_shows_only_active_categories_with_posts(): void
    {
        $activeCategory = BlogCategory::factory()->create([
            'is_active' => true,
            'language' => 'en',
        ]);
        $inactiveCategory = BlogCategory::factory()->create([
            'is_active' => false,
            'language' => 'en',
        ]);

        BlogPost::factory()->published()->create([
            'blog_category_id' => $activeCategory->id,
            'language' => 'en',
        ]);

        $response = $this->get(route('blog.index', ['locale' => 'en']));

        $categories = $response->viewData('categories');
        $this->assertTrue($categories->contains('id', $activeCategory->id));
        $this->assertFalse($categories->contains('id', $inactiveCategory->id));
    }

    public function test_sidebar_shows_recent_posts(): void
    {
        BlogPost::factory()->published()->count(10)->create(['language' => 'en']);

        $response = $this->get(route('blog.index', ['locale' => 'en']));

        $recentPosts = $response->viewData('recentPosts');
        $this->assertCount(5, $recentPosts); // Limited to 5
    }
}
