<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário admin para os testes
        $this->adminUser = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com'
        ]);
    }

    #[Test]
    public function admin_can_view_dashboard()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_users_index()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_users_create()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_store_user()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => false
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function admin_can_view_user_details()
    {
        $this->actingAs($this->adminUser);

        $user = User::factory()->create();

        $response = $this->get(route('admin.users.show', $user));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_edit_user_form()
    {
        $this->actingAs($this->adminUser);

        $user = User::factory()->create();

        $response = $this->get(route('admin.users.edit', $user));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_update_user()
    {
        $this->actingAs($this->adminUser);

        $user = User::factory()->create();

        $response = $this->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'is_admin' => false
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function admin_can_delete_user()
    {
        $this->actingAs($this->adminUser);

        $user = User::factory()->create();

        $response = $this->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function non_admin_cannot_access_admin_routes()
    {
        $regularUser = User::factory()->regular()->create();
        $this->actingAs($regularUser);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function guest_is_redirected_to_login()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }
}