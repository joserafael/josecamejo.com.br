<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_non_admin_cannot_access_posts_index(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.posts.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_posts_index(): void
    {
        $response = $this->get(route('admin.posts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_posts_create(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.posts.create'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_posts_create(): void
    {
        $response = $this->get(route('admin.posts.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_posts_show(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.posts.show', ['post' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_posts_show(): void
    {
        $response = $this->get(route('admin.posts.show', ['post' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_posts_edit(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.posts.edit', ['post' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_posts_edit(): void
    {
        $response = $this->get(route('admin.posts.edit', ['post' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_store_post(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post(route('admin.posts.store'), []);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_store_post(): void
    {
        $response = $this->post(route('admin.posts.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_update_post(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->put(route('admin.posts.update', ['post' => 1]), []);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_update_post(): void
    {
        $response = $this->put(route('admin.posts.update', ['post' => 1]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_delete_post(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->delete(route('admin.posts.destroy', ['post' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_delete_post(): void
    {
        $response = $this->delete(route('admin.posts.destroy', ['post' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_publish_post(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post(route('admin.posts.publish', ['post' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_publish_post(): void
    {
        $response = $this->post(route('admin.posts.publish', ['post' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_unpublish_post(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post(route('admin.posts.unpublish', ['post' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_unpublish_post(): void
    {
        $response = $this->post(route('admin.posts.unpublish', ['post' => 1]));

        $response->assertRedirect(route('login'));
    }
}
