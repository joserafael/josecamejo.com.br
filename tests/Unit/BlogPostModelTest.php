<?php

namespace Tests\Unit;

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\BlogSubcategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogPostModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $post = new BlogPost;

        $expected = [
            'title', 'slug', 'excerpt', 'content', 'featured_image',
            'meta_title', 'meta_description', 'language', 'status',
            'is_featured', 'allow_comments', 'views_count', 'sort_order',
            'published_at', 'blog_category_id', 'blog_subcategory_id', 'user_id',
        ];

        $this->assertEquals($expected, $post->getFillable());
    }

    #[Test]
    public function it_has_correct_casts(): void
    {
        $post = new BlogPost;

        $casts = $post->getCasts();

        $this->assertEquals('boolean', $casts['is_featured']);
        $this->assertEquals('boolean', $casts['allow_comments']);
        $this->assertEquals('integer', $casts['views_count']);
        $this->assertEquals('integer', $casts['sort_order']);
        $this->assertEquals('datetime', $casts['published_at']);
    }

    #[Test]
    public function it_belongs_to_category(): void
    {
        $category = BlogCategory::factory()->create();
        $post = BlogPost::factory()->create(['blog_category_id' => $category->id]);

        $this->assertInstanceOf(BlogCategory::class, $post->category);
        $this->assertEquals($category->id, $post->category->id);
    }

    #[Test]
    public function it_belongs_to_subcategory(): void
    {
        $category = BlogCategory::factory()->create();
        $subcategory = BlogSubcategory::factory()->create(['blog_category_id' => $category->id]);
        $post = BlogPost::factory()->create(['blog_subcategory_id' => $subcategory->id]);

        $this->assertInstanceOf(BlogSubcategory::class, $post->subcategory);
        $this->assertEquals($subcategory->id, $post->subcategory->id);
    }

    #[Test]
    public function it_belongs_to_author(): void
    {
        $user = User::factory()->create();
        $post = BlogPost::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $post->author);
        $this->assertEquals($user->id, $post->author->id);
    }

    #[Test]
    public function it_has_many_tags(): void
    {
        $post = BlogPost::factory()->create();
        $tag1 = BlogTag::factory()->create();
        $tag2 = BlogTag::factory()->create();

        $post->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertCount(2, $post->tags);
        $this->assertTrue($post->tags->contains($tag1));
        $this->assertTrue($post->tags->contains($tag2));
    }

    #[Test]
    public function it_has_many_comments(): void
    {
        $post = BlogPost::factory()->create();
        $comment1 = BlogComment::factory()->create(['blog_post_id' => $post->id]);
        $comment2 = BlogComment::factory()->create(['blog_post_id' => $post->id]);

        $this->assertCount(2, $post->comments);
        $this->assertTrue($post->comments->contains($comment1));
        $this->assertTrue($post->comments->contains($comment2));
    }

    #[Test]
    public function it_generates_slug_on_create(): void
    {
        $post = BlogPost::factory()->create([
            'title' => 'Test Blog Post Title',
            'slug' => null,
        ]);

        $this->assertNotNull($post->slug);
        $this->assertEquals('test-blog-post-title', $post->slug);
    }

    #[Test]
    public function it_generates_unique_slugs(): void
    {
        $post1 = BlogPost::factory()->create([
            'title' => 'Test Post',
            'slug' => null,
        ]);

        $post2 = BlogPost::factory()->create([
            'title' => 'Test Post',
            'slug' => null,
        ]);

        $this->assertNotEquals($post1->slug, $post2->slug);
        $this->assertEquals('test-post', $post1->slug);
        $this->assertEquals('test-post-1', $post2->slug);
    }

    #[Test]
    public function it_sets_published_at_when_published(): void
    {
        $post = BlogPost::factory()->create([
            'status' => 'published',
            'published_at' => null,
        ]);

        $this->assertNotNull($post->published_at);
    }

    #[Test]
    public function it_does_not_set_published_at_for_draft(): void
    {
        $post = BlogPost::factory()->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->assertNull($post->published_at);
    }

    #[Test]
    public function scope_published_returns_only_published_posts(): void
    {
        BlogPost::factory()->published()->create();
        BlogPost::factory()->draft()->create();

        $publishedPosts = BlogPost::published()->get();

        $this->assertCount(1, $publishedPosts);
        $this->assertEquals('published', $publishedPosts->first()->status);
    }

    #[Test]
    public function scope_draft_returns_only_draft_posts(): void
    {
        BlogPost::factory()->published()->create();
        BlogPost::factory()->draft()->create();

        $draftPosts = BlogPost::draft()->get();

        $this->assertCount(1, $draftPosts);
        $this->assertEquals('draft', $draftPosts->first()->status);
    }

    #[Test]
    public function scope_featured_returns_only_featured_posts(): void
    {
        BlogPost::factory()->featured()->create();
        BlogPost::factory()->create(['is_featured' => false]);

        $featuredPosts = BlogPost::featured()->get();

        $this->assertCount(1, $featuredPosts);
        $this->assertTrue($featuredPosts->first()->is_featured);
    }

    #[Test]
    public function scope_in_language_filters_by_language(): void
    {
        BlogPost::factory()->create(['language' => 'pt']);
        BlogPost::factory()->create(['language' => 'en']);

        $ptPosts = BlogPost::inLanguage('pt')->get();

        $this->assertCount(1, $ptPosts);
        $this->assertEquals('pt', $ptPosts->first()->language);
    }

    #[Test]
    public function scope_by_category_filters_by_category(): void
    {
        $category = BlogCategory::factory()->create();
        BlogPost::factory()->create(['blog_category_id' => $category->id]);
        BlogPost::factory()->create();

        $categoryPosts = BlogPost::byCategory($category->id)->get();

        $this->assertCount(1, $categoryPosts);
        $this->assertEquals($category->id, $categoryPosts->first()->blog_category_id);
    }

    #[Test]
    public function it_can_check_if_published(): void
    {
        $publishedPost = BlogPost::factory()->create([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $draftPost = BlogPost::factory()->create(['status' => 'draft']);

        $this->assertTrue($publishedPost->isPublished());
        $this->assertFalse($draftPost->isPublished());
    }

    #[Test]
    public function it_can_increment_views(): void
    {
        $post = BlogPost::factory()->create(['views_count' => 10]);

        $post->incrementViews();
        $post->refresh();

        $this->assertEquals(11, $post->views_count);
    }

    #[Test]
    public function it_gets_excerpt_attribute(): void
    {
        $post = BlogPost::factory()->create([
            'excerpt' => 'Custom excerpt',
            'content' => 'Full content here',
        ]);

        $this->assertEquals('Custom excerpt', $post->excerpt);
    }

    #[Test]
    public function it_generates_excerpt_from_content(): void
    {
        $post = BlogPost::factory()->create([
            'excerpt' => null,
            'content' => '<p>This is some HTML content with more than 150 characters that should be truncated to create an excerpt.</p>',
        ]);

        $this->assertNotNull($post->excerpt);
        $this->assertLessThanOrEqual(153, strlen($post->excerpt));
    }

    #[Test]
    public function it_calculates_reading_time(): void
    {
        $content = str_repeat('word ', 400);
        $post = BlogPost::factory()->create(['content' => $content]);

        $this->assertEquals('2 min read', $post->readingTime);
    }
}
