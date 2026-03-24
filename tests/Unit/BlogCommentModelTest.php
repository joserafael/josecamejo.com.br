<?php

namespace Tests\Unit;

use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogCommentModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $comment = new BlogComment;

        $expected = [
            'blog_post_id', 'parent_id', 'author_name', 'author_email',
            'author_website', 'content', 'status', 'ip_address',
            'user_agent', 'approved_at', 'approved_by',
        ];

        $this->assertEquals($expected, $comment->getFillable());
    }

    #[Test]
    public function it_has_correct_casts(): void
    {
        $comment = new BlogComment;

        $casts = $comment->getCasts();

        $this->assertEquals('datetime', $casts['approved_at']);
    }

    #[Test]
    public function it_belongs_to_blog_post(): void
    {
        $post = BlogPost::factory()->create();
        $comment = BlogComment::factory()->create(['blog_post_id' => $post->id]);

        $this->assertInstanceOf(BlogPost::class, $comment->blogPost);
        $this->assertEquals($post->id, $comment->blogPost->id);
    }

    #[Test]
    public function it_belongs_to_parent_comment(): void
    {
        $post = BlogPost::factory()->create();
        $parentComment = BlogComment::factory()->create(['blog_post_id' => $post->id]);
        $reply = BlogComment::factory()->create([
            'blog_post_id' => $post->id,
            'parent_id' => $parentComment->id,
        ]);

        $this->assertInstanceOf(BlogComment::class, $reply->parent);
        $this->assertEquals($parentComment->id, $reply->parent->id);
    }

    #[Test]
    public function it_has_many_replies(): void
    {
        $post = BlogPost::factory()->create();
        $parentComment = BlogComment::factory()->create(['blog_post_id' => $post->id]);
        $reply1 = BlogComment::factory()->create([
            'blog_post_id' => $post->id,
            'parent_id' => $parentComment->id,
        ]);
        $reply2 = BlogComment::factory()->create([
            'blog_post_id' => $post->id,
            'parent_id' => $parentComment->id,
        ]);

        $this->assertCount(2, $parentComment->replies);
        $this->assertTrue($parentComment->replies->contains($reply1));
        $this->assertTrue($parentComment->replies->contains($reply2));
    }

    #[Test]
    public function it_belongs_to_approved_by_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $comment = BlogComment::factory()->approved()->create([
            'approved_by' => $admin->id,
        ]);

        $this->assertInstanceOf(User::class, $comment->approvedBy);
        $this->assertEquals($admin->id, $comment->approvedBy->id);
    }

    #[Test]
    public function scope_approved_returns_only_approved_comments(): void
    {
        BlogComment::factory()->approved()->create();
        BlogComment::factory()->pending()->create();
        BlogComment::factory()->rejected()->create();

        $approvedComments = BlogComment::approved()->get();

        $this->assertCount(1, $approvedComments);
        $this->assertEquals('approved', $approvedComments->first()->status);
    }

    #[Test]
    public function scope_pending_returns_only_pending_comments(): void
    {
        BlogComment::factory()->approved()->create();
        BlogComment::factory()->pending()->create();

        $pendingComments = BlogComment::pending()->get();

        $this->assertCount(1, $pendingComments);
        $this->assertEquals('pending', $pendingComments->first()->status);
    }

    #[Test]
    public function scope_top_level_returns_only_top_level_comments(): void
    {
        $post = BlogPost::factory()->create();
        $parentComment = BlogComment::factory()->create(['blog_post_id' => $post->id]);
        BlogComment::factory()->reply($parentComment)->create();

        $topLevelComments = BlogComment::topLevel()->get();

        $this->assertCount(1, $topLevelComments);
        $this->assertEquals($parentComment->id, $topLevelComments->first()->id);
    }

    #[Test]
    public function it_can_check_if_approved(): void
    {
        $approvedComment = BlogComment::factory()->approved()->create();
        $pendingComment = BlogComment::factory()->pending()->create();

        $this->assertTrue($approvedComment->isApproved());
        $this->assertFalse($pendingComment->isApproved());
    }

    #[Test]
    public function it_can_check_if_pending(): void
    {
        $approvedComment = BlogComment::factory()->approved()->create();
        $pendingComment = BlogComment::factory()->pending()->create();

        $this->assertFalse($approvedComment->isPending());
        $this->assertTrue($pendingComment->isPending());
    }

    #[Test]
    public function it_can_check_if_rejected(): void
    {
        $approvedComment = BlogComment::factory()->approved()->create();
        $rejectedComment = BlogComment::factory()->rejected()->create();

        $this->assertFalse($approvedComment->isRejected());
        $this->assertTrue($rejectedComment->isRejected());
    }

    #[Test]
    public function it_can_check_if_reply(): void
    {
        $post = BlogPost::factory()->create();
        $parentComment = BlogComment::factory()->create(['blog_post_id' => $post->id]);
        $reply = BlogComment::factory()->reply($parentComment)->create();

        $this->assertFalse($parentComment->isReply());
        $this->assertTrue($reply->isReply());
    }

    #[Test]
    public function it_can_approve_comment(): void
    {
        $comment = BlogComment::factory()->pending()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $result = $comment->approve($admin->id);

        $this->assertTrue($result);
        $this->assertEquals('approved', $comment->status);
        $this->assertNotNull($comment->approved_at);
        $this->assertEquals($admin->id, $comment->approved_by);
    }

    #[Test]
    public function it_can_reject_comment(): void
    {
        $comment = BlogComment::factory()->approved()->create();

        $result = $comment->reject();

        $this->assertTrue($result);
        $this->assertEquals('rejected', $comment->status);
        $this->assertNull($comment->approved_at);
        $this->assertNull($comment->approved_by);
    }

    #[Test]
    public function it_generates_gravatar_url(): void
    {
        $comment = BlogComment::factory()->create([
            'author_email' => 'test@example.com',
        ]);

        $gravatarUrl = $comment->gravatar;
        $expectedHash = md5(strtolower(trim('test@example.com')));

        $this->assertStringContainsString($expectedHash, $gravatarUrl);
        $this->assertStringContainsString('gravatar.com/avatar/', $gravatarUrl);
    }

    #[Test]
    public function it_can_be_created_with_factory(): void
    {
        $comment = BlogComment::factory()->create();

        $this->assertDatabaseHas('blog_comments', [
            'id' => $comment->id,
            'author_name' => $comment->author_name,
            'author_email' => $comment->author_email,
        ]);
    }

    #[Test]
    public function it_can_be_created_as_approved(): void
    {
        $comment = BlogComment::factory()->approved()->create();

        $this->assertEquals('approved', $comment->status);
        $this->assertNotNull($comment->approved_at);
    }

    #[Test]
    public function it_can_be_created_as_pending(): void
    {
        $comment = BlogComment::factory()->pending()->create();

        $this->assertEquals('pending', $comment->status);
        $this->assertNull($comment->approved_at);
    }

    #[Test]
    public function it_can_be_created_as_rejected(): void
    {
        $comment = BlogComment::factory()->rejected()->create();

        $this->assertEquals('rejected', $comment->status);
        $this->assertNull($comment->approved_at);
    }

    #[Test]
    public function it_can_be_created_as_reply(): void
    {
        $post = BlogPost::factory()->create();
        $parentComment = BlogComment::factory()->create(['blog_post_id' => $post->id]);
        $reply = BlogComment::factory()->reply($parentComment)->create();

        $this->assertNotNull($reply->parent_id);
        $this->assertEquals($parentComment->id, $reply->parent_id);
        $this->assertEquals($post->id, $reply->blog_post_id);
    }
}
