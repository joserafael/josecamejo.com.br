<?php

namespace Tests\Feature\Admin;

use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogCommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_view_comments_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog-comments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-comments.index');
    }

    public function test_non_admin_cannot_access_comments_index(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.blog-comments.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_comments_index(): void
    {
        $response = $this->get(route('admin.blog-comments.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.blog-comments.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-comments.create');
    }

    public function test_admin_can_store_comment(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();

        $data = [
            'blog_post_id' => $post->id,
            'author_name' => 'Test Author',
            'author_email' => 'test@example.com',
            'content' => 'This is a test comment content.',
            'status' => 'pending',
        ];

        $response = $this->post(route('admin.blog-comments.store'), $data);

        $response->assertRedirect(route('admin.blog-comments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('blog_comments', [
            'author_name' => 'Test Author',
            'author_email' => 'test@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_store_approved_comment(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();

        $data = [
            'blog_post_id' => $post->id,
            'author_name' => 'Test Author',
            'author_email' => 'test@example.com',
            'content' => 'This is a test comment content.',
            'status' => 'approved',
        ];

        $response = $this->post(route('admin.blog-comments.store'), $data);

        $this->assertDatabaseHas('blog_comments', [
            'author_name' => 'Test Author',
            'status' => 'approved',
        ]);

        $comment = BlogComment::where('author_name', 'Test Author')->first();
        $this->assertNotNull($comment->approved_at);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.blog-comments.store'), []);

        $response->assertSessionHasErrors(['blog_post_id', 'author_name', 'author_email', 'content', 'status']);
    }

    public function test_admin_can_view_comment(): void
    {
        $this->actingAs($this->admin);

        $comment = BlogComment::factory()->create();

        $response = $this->get(route('admin.blog-comments.show', $comment));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-comments.show');
        $response->assertViewHas('blogComment', $comment);
    }

    public function test_admin_can_view_edit_form(): void
    {
        $this->actingAs($this->admin);

        $comment = BlogComment::factory()->create();

        $response = $this->get(route('admin.blog-comments.edit', $comment));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog-comments.edit');
        $response->assertViewHas('blogComment', $comment);
    }

    public function test_admin_can_update_comment(): void
    {
        $this->actingAs($this->admin);

        $comment = BlogComment::factory()->create([
            'author_name' => 'Original Name',
        ]);

        $data = [
            'blog_post_id' => $comment->blog_post_id,
            'author_name' => 'Updated Name',
            'author_email' => 'updated@example.com',
            'content' => 'Updated content',
            'status' => 'approved',
        ];

        $response = $this->put(route('admin.blog-comments.update', $comment), $data);

        $response->assertRedirect(route('admin.blog-comments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('blog_comments', [
            'id' => $comment->id,
            'author_name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_comment(): void
    {
        $this->actingAs($this->admin);

        $comment = BlogComment::factory()->create();

        $response = $this->delete(route('admin.blog-comments.destroy', $comment));

        $response->assertRedirect(route('admin.blog-comments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('blog_comments', ['id' => $comment->id]);
    }

    public function test_admin_can_approve_comment(): void
    {
        $this->actingAs($this->admin);

        $comment = BlogComment::factory()->pending()->create();

        $response = $this->post(route('admin.blog-comments.approve', $comment));

        $response->assertSessionHas('success');

        $comment->refresh();
        $this->assertEquals('approved', $comment->status);
        $this->assertNotNull($comment->approved_at);
    }

    public function test_admin_can_reject_comment(): void
    {
        $this->actingAs($this->admin);

        $comment = BlogComment::factory()->approved()->create();

        $response = $this->post(route('admin.blog-comments.reject', $comment));

        $response->assertSessionHas('success');

        $comment->refresh();
        $this->assertEquals('rejected', $comment->status);
    }

    public function test_admin_can_bulk_approve_comments(): void
    {
        $this->actingAs($this->admin);

        $comment1 = BlogComment::factory()->pending()->create();
        $comment2 = BlogComment::factory()->pending()->create();

        $response = $this->post(route('admin.blog-comments.bulk-action'), [
            'action' => 'approve',
            'comment_ids' => [$comment1->id, $comment2->id],
        ]);

        $response->assertSessionHas('success');

        $comment1->refresh();
        $comment2->refresh();

        $this->assertEquals('approved', $comment1->status);
        $this->assertEquals('approved', $comment2->status);
    }

    public function test_admin_can_bulk_reject_comments(): void
    {
        $this->actingAs($this->admin);

        $comment1 = BlogComment::factory()->approved()->create();
        $comment2 = BlogComment::factory()->approved()->create();

        $response = $this->post(route('admin.blog-comments.bulk-action'), [
            'action' => 'reject',
            'comment_ids' => [$comment1->id, $comment2->id],
        ]);

        $response->assertSessionHas('success');

        $comment1->refresh();
        $comment2->refresh();

        $this->assertEquals('rejected', $comment1->status);
        $this->assertEquals('rejected', $comment2->status);
    }

    public function test_admin_can_bulk_delete_comments(): void
    {
        $this->actingAs($this->admin);

        $comment1 = BlogComment::factory()->create();
        $comment2 = BlogComment::factory()->create();

        $response = $this->post(route('admin.blog-comments.bulk-action'), [
            'action' => 'delete',
            'comment_ids' => [$comment1->id, $comment2->id],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('blog_comments', ['id' => $comment1->id]);
        $this->assertDatabaseMissing('blog_comments', ['id' => $comment2->id]);
    }

    public function test_delete_also_deletes_replies(): void
    {
        $this->actingAs($this->admin);

        $post = BlogPost::factory()->create();
        $parentComment = BlogComment::factory()->create(['blog_post_id' => $post->id]);
        $reply = BlogComment::factory()->reply($parentComment)->create();

        $response = $this->delete(route('admin.blog-comments.destroy', $parentComment));

        $response->assertRedirect(route('admin.blog-comments.index'));

        $this->assertDatabaseMissing('blog_comments', ['id' => $parentComment->id]);
        $this->assertDatabaseMissing('blog_comments', ['id' => $reply->id]);
    }

    public function test_index_pagination(): void
    {
        $this->actingAs($this->admin);

        BlogComment::factory()->count(25)->create();

        $response = $this->get(route('admin.blog-comments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('comments');
    }

    public function test_index_filter_by_status(): void
    {
        $this->actingAs($this->admin);

        BlogComment::factory()->approved()->create();
        BlogComment::factory()->pending()->create();

        $response = $this->get(route('admin.blog-comments.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertViewHas('comments');
    }

    public function test_index_search(): void
    {
        $this->actingAs($this->admin);

        BlogComment::factory()->create(['author_name' => 'Searchable Author']);
        BlogComment::factory()->create(['author_name' => 'Other Author']);

        $response = $this->get(route('admin.blog-comments.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
        $response->assertViewHas('comments');
    }
}
