<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_view_users_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_users_index(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_users_index(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_users_create(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_users_create(): void
    {
        $response = $this->get(route('admin.users.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_users_show(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.users.show', ['user' => $otherUser]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_users_show(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.show', ['user' => $user->id]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_users_edit(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.users.edit', ['user' => $otherUser]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_users_edit(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.edit', ['user' => $user->id]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_store_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post(route('admin.users.store'), []);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_store_user(): void
    {
        $response = $this->post(route('admin.users.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_update_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put(route('admin.users.update', ['user' => $otherUser]), []);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_update_user(): void
    {
        $user = User::factory()->create();

        $response = $this->put(route('admin.users.update', ['user' => $user->id]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_delete_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete(route('admin.users.destroy', ['user' => $otherUser]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('admin.users.destroy', ['user' => $user->id]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_change_password(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('admin.users.change-password', ['user' => $otherUser]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_change_password(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.change-password', ['user' => $user->id]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_update_password(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put(route('admin.users.update-password', ['user' => $otherUser]), []);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this->put(route('admin.users.update-password', ['user' => $user->id]), []);

        $response->assertRedirect(route('login'));
    }
}
