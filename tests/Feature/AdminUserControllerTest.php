<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário admin para os testes
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'is_admin' => true,
            'password' => Hash::make('password123')
        ]);

        // Criar usuário regular para os testes
        $this->regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'is_admin' => false,
            'password' => Hash::make('password123')
        ]);
    }

    #[Test]
    public function admin_can_view_users_index()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    #[Test]
    public function non_admin_cannot_access_users_index()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_access_users_index()
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_can_search_users()
    {
        $this->actingAs($this->adminUser);

        // Criar usuários adicionais para busca
        User::factory()->create(['name' => 'João Silva', 'email' => 'joao@test.com']);
        User::factory()->create(['name' => 'Maria Santos', 'email' => 'maria@test.com']);

        $response = $this->get(route('admin.users.index', ['search' => 'João']));

        $response->assertStatus(200);
        $response->assertSee('João Silva');
        $response->assertDontSee('Maria Santos');
    }

    #[Test]
    public function admin_can_filter_users_by_type()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.index', ['type' => 'admin']));

        $response->assertStatus(200);
        $response->assertSee($this->adminUser->name);
        $response->assertDontSee($this->regularUser->name);
    }

    #[Test]
    public function admin_can_view_create_user_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    #[Test]
    public function admin_can_create_new_user()
    {
        $this->actingAs($this->adminUser);

        $userData = [
            'name' => 'Novo Usuário',
            'email' => 'novo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => false
        ];

        $response = $this->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'Usuário criado com sucesso!');

        $this->assertDatabaseHas('users', [
            'name' => 'Novo Usuário',
            'email' => 'novo@test.com',
            'is_admin' => false
        ]);
    }

    #[Test]
    public function admin_can_create_admin_user()
    {
        $this->actingAs($this->adminUser);

        $userData = [
            'name' => 'Novo Admin',
            'email' => 'novoadmin@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => true
        ];

        $response = $this->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        
        $this->assertDatabaseHas('users', [
            'name' => 'Novo Admin',
            'email' => 'novoadmin@test.com',
            'is_admin' => true
        ]);
    }

    #[Test]
    public function create_user_validates_required_fields()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function create_user_validates_unique_email()
    {
        $this->actingAs($this->adminUser);

        $userData = [
            'name' => 'Teste',
            'email' => $this->regularUser->email, // Email já existente
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post(route('admin.users.store'), $userData);

        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function admin_can_view_user_details()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.show', $this->regularUser));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user', $this->regularUser);
    }

    #[Test]
    public function admin_can_view_edit_user_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.edit', $this->regularUser));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user', $this->regularUser);
    }

    #[Test]
    public function admin_can_update_user()
    {
        $this->actingAs($this->adminUser);

        $updateData = [
            'name' => 'Nome Atualizado',
            'email' => 'atualizado@test.com',
            'is_admin' => true
        ];

        $response = $this->put(route('admin.users.update', $this->regularUser), $updateData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'Usuário atualizado com sucesso!');

        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id,
            'name' => 'Nome Atualizado',
            'email' => 'atualizado@test.com',
            'is_admin' => true
        ]);
    }

    #[Test]
    public function update_user_validates_unique_email()
    {
        $this->actingAs($this->adminUser);

        $updateData = [
            'name' => 'Nome Teste',
            'email' => $this->adminUser->email, // Email já usado por outro usuário
            'is_admin' => false
        ];

        $response = $this->put(route('admin.users.update', $this->regularUser), $updateData);

        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function admin_can_delete_user()
    {
        $this->actingAs($this->adminUser);

        $userToDelete = User::factory()->create();

        $response = $this->delete(route('admin.users.destroy', $userToDelete));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'Usuário deletado com sucesso!');

        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id
        ]);
    }

    #[Test]
    public function admin_cannot_delete_themselves()
    {
        $this->actingAs($this->adminUser);

        $response = $this->delete(route('admin.users.destroy', $this->adminUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'Você não pode deletar sua própria conta!');

        $this->assertDatabaseHas('users', [
            'id' => $this->adminUser->id
        ]);
    }

    #[Test]
    public function admin_can_view_change_password_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.change-password', $this->regularUser));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.change-password');
        $response->assertViewHas('user', $this->regularUser);
    }

    #[Test]
    public function admin_can_change_user_password()
    {
        $this->actingAs($this->adminUser);

        $passwordData = [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->put(route('admin.users.update-password', $this->regularUser), $passwordData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'Senha alterada com sucesso!');

        // Verificar se a senha foi alterada
        $this->regularUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->regularUser->password));
    }

    #[Test]
    public function change_password_validates_confirmation()
    {
        $this->actingAs($this->adminUser);

        $passwordData = [
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword'
        ];

        $response = $this->put(route('admin.users.update-password', $this->regularUser), $passwordData);

        $response->assertSessionHasErrors(['password']);
    }

    #[Test]
    public function change_password_validates_required_fields()
    {
        $this->actingAs($this->adminUser);

        $response = $this->put(route('admin.users.update-password', $this->regularUser), []);

        $response->assertSessionHasErrors(['password']);
    }
}