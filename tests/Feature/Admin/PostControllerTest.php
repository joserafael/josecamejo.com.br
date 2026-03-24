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
}
