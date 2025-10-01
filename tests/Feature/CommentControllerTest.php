<?php

namespace Tests\Feature;

use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $blogPost;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a blog post that allows comments
        $this->blogPost = BlogPost::factory()->create([
            'allow_comments' => true,
            'status' => 'published'
        ]);
    }

    /** @test */
    public function it_can_store_a_valid_comment()
    {
        $commentData = [
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'author_website' => 'https://johndoe.com',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        $response = $this->post(route('comments.store', $this->blogPost), $commentData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Your comment has been submitted and is awaiting approval.');

        $this->assertDatabaseHas('blog_comments', [
            'blog_post_id' => $this->blogPost->id,
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'author_website' => 'https://johndoe.com',
            'content' => 'This is a test comment with enough content to pass validation.',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_can_store_a_reply_comment()
    {
        // Create a parent comment
        $parentComment = BlogComment::factory()->create([
            'blog_post_id' => $this->blogPost->id,
            'status' => 'approved'
        ]);

        $replyData = [
            'parent_id' => $parentComment->id,
            'author_name' => 'Jane Doe',
            'author_email' => 'jane@example.com',
            'content' => 'This is a reply to the parent comment with sufficient content.'
        ];

        $response = $this->post(route('comments.store', $this->blogPost), $replyData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('blog_comments', [
            'blog_post_id' => $this->blogPost->id,
            'parent_id' => $parentComment->id,
            'author_name' => 'Jane Doe',
            'author_email' => 'jane@example.com',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->post(route('comments.store', $this->blogPost), []);

        $response->assertSessionHasErrors(['author_name', 'author_email', 'content']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $commentData = [
            'author_name' => 'John Doe',
            'author_email' => 'invalid-email',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        $response = $this->post(route('comments.store', $this->blogPost), $commentData);

        $response->assertSessionHasErrors(['author_email']);
    }

    /** @test */
    public function it_validates_content_length()
    {
        $commentData = [
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'content' => 'Short' // Too short
        ];

        $response = $this->post(route('comments.store', $this->blogPost), $commentData);

        $response->assertSessionHasErrors(['content']);
    }

    /** @test */
    public function it_validates_website_url_format()
    {
        $commentData = [
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'author_website' => 'invalid-url',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        $response = $this->post(route('comments.store', $this->blogPost), $commentData);

        $response->assertSessionHasErrors(['author_website']);
    }

    /** @test */
    public function it_prevents_comments_on_posts_that_dont_allow_them()
    {
        $blogPost = BlogPost::factory()->create([
            'allow_comments' => false,
            'status' => 'published'
        ]);

        $commentData = [
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        $response = $this->post(route('comments.store', $blogPost), $commentData);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_prevents_comments_on_unpublished_posts()
    {
        $blogPost = BlogPost::factory()->create([
            'allow_comments' => true,
            'status' => 'draft'
        ]);

        $commentData = [
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        $response = $this->post(route('comments.store', $blogPost), $commentData);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_get_comments_for_a_post()
    {
        // Create some approved comments
        $comment1 = BlogComment::factory()->create([
            'blog_post_id' => $this->blogPost->id,
            'status' => 'approved',
            'parent_id' => null
        ]);

        $comment2 = BlogComment::factory()->create([
            'blog_post_id' => $this->blogPost->id,
            'status' => 'approved',
            'parent_id' => $comment1->id
        ]);

        // Create a pending comment (should not be included)
        BlogComment::factory()->create([
            'blog_post_id' => $this->blogPost->id,
            'status' => 'pending'
        ]);

        $response = $this->get(route('comments.get', $this->blogPost));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'comments',
            'total'
        ]);

        $data = $response->json();
        $this->assertCount(1, $data['comments']); // Only top-level approved comments
        $this->assertEquals(1, $data['total']);
    }

    /** @test */
    public function it_prevents_getting_comments_for_posts_that_dont_allow_them()
    {
        $blogPost = BlogPost::factory()->create([
            'allow_comments' => false,
            'status' => 'published'
        ]);

        $response = $this->get(route('comments.get', $blogPost));

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Comments are not allowed for this post.']);
    }

    /** @test */
    public function it_applies_rate_limiting()
    {
        // Clear any existing rate limits
        RateLimiter::clear('comment-submission:127.0.0.1');

        $commentData = [
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        // First comment should succeed
        $response1 = $this->post(route('comments.store', $this->blogPost), $commentData);
        $response1->assertRedirect();

        // Simulate hitting rate limit by manually setting it
        RateLimiter::hit('comment-submission:127.0.0.1', 300);
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('comment-submission:127.0.0.1', 300);
        }

        // Next comment should be rate limited
        $response2 = $this->post(route('comments.store', $this->blogPost), $commentData);
        $response2->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function it_validates_parent_comment_exists()
    {
        $commentData = [
            'parent_id' => 99999, // Non-existent parent
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        $response = $this->post(route('comments.store', $this->blogPost), $commentData);

        $response->assertSessionHasErrors(['parent_id']);
    }

    /** @test */
    public function it_validates_parent_comment_belongs_to_same_post()
    {
        $otherPost = BlogPost::factory()->create(['allow_comments' => true]);
        $parentComment = BlogComment::factory()->create([
            'blog_post_id' => $otherPost->id,
            'status' => 'approved'
        ]);

        $commentData = [
            'parent_id' => $parentComment->id,
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
            'content' => 'This is a test comment with enough content to pass validation.'
        ];

        $response = $this->post(route('comments.store', $this->blogPost), $commentData);

        $response->assertSessionHasErrors(['parent_id']);
    }

    protected function tearDown(): void
    {
        // Clear rate limits after each test
        RateLimiter::clear('comment-submission:127.0.0.1');
        parent::tearDown();
    }
}