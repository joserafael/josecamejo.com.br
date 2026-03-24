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
}
