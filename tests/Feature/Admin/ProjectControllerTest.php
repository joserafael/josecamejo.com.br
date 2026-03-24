<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_non_admin_cannot_access_projects_index(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.projects.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_projects_index(): void
    {
        $response = $this->get(route('admin.projects.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_projects_create(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.projects.create'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_projects_create(): void
    {
        $response = $this->get(route('admin.projects.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_projects_show(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.projects.show', ['project' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_projects_show(): void
    {
        $response = $this->get(route('admin.projects.show', ['project' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_projects_edit(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->get(route('admin.projects.edit', ['project' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_projects_edit(): void
    {
        $response = $this->get(route('admin.projects.edit', ['project' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_store_project(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post(route('admin.projects.store'), []);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_store_project(): void
    {
        $response = $this->post(route('admin.projects.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_update_project(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->put(route('admin.projects.update', ['project' => 1]), []);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_update_project(): void
    {
        $response = $this->put(route('admin.projects.update', ['project' => 1]), []);

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_delete_project(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->delete(route('admin.projects.destroy', ['project' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_delete_project(): void
    {
        $response = $this->delete(route('admin.projects.destroy', ['project' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_feature_project(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post(route('admin.projects.feature', ['project' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_feature_project(): void
    {
        $response = $this->post(route('admin.projects.feature', ['project' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_unfeature_project(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post(route('admin.projects.unfeature', ['project' => 1]));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_unfeature_project(): void
    {
        $response = $this->post(route('admin.projects.unfeature', ['project' => 1]));

        $response->assertRedirect(route('login'));
    }
}
