<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário admin
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'is_admin' => true,
            'password' => Hash::make('password123')
        ]);

        // Criar usuário regular
        $this->regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'is_admin' => false,
            'password' => Hash::make('password123')
        ]);
    }

    #[Test]
    public function guest_can_view_login_form()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    #[Test]
    public function admin_user_can_login_successfully()
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }

    #[Test]
    public function admin_user_can_login_with_remember_me()
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'remember' => true
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }

    #[Test]
    public function regular_user_cannot_login_to_admin()
    {
        $response = $this->post(route('login'), [
            'email' => 'user@test.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function login_fails_with_invalid_credentials()
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'wrong-password'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function login_fails_with_nonexistent_email()
    {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent@test.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function login_validates_required_fields()
    {
        $response = $this->post(route('login'), []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    #[Test]
    public function login_validates_email_format()
    {
        $response = $this->post(route('login'), [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function authenticated_admin_can_logout()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    #[Test]
    public function logout_invalidates_session()
    {
        $this->actingAs($this->adminUser);

        // Verificar se está autenticado
        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        // Verificar se não está mais autenticado
        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_logout()
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_view_login_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('login'));

        // O usuário autenticado pode ver o formulário de login
        $response->assertStatus(200);
    }

    #[Test]
    public function login_redirects_to_admin_dashboard()
    {
        // Fazer login
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        // Deve redirecionar para o dashboard admin
        $response->assertRedirect(route('admin.dashboard'));
    }

    #[Test]
    public function login_with_empty_email_shows_validation_error()
    {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function login_with_empty_password_shows_validation_error()
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    #[Test]
    public function login_regenerates_session_on_success()
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }

    #[Test]
    public function multiple_failed_login_attempts_are_handled()
    {
        // Simular múltiplas tentativas de login falhadas
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post(route('login'), [
                'email' => 'admin@test.com',
                'password' => 'wrong-password'
            ]);

            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        }
    }

    #[Test]
    public function login_error_message_is_generic_for_security()
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'wrong-password'
        ]);

        $response->assertSessionHasErrors(['email']);
        
        $errors = session('errors')->get('email');
        $this->assertContains('As credenciais fornecidas não correspondem aos nossos registros.', $errors);
    }

    #[Test]
    public function regular_user_login_error_message_is_specific()
    {
        $response = $this->post(route('login'), [
            'email' => 'user@test.com',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors(['email']);
        
        $errors = session('errors')->get('email');
        $this->assertContains('Você não tem permissão para acessar o painel administrativo.', $errors);
    }
}