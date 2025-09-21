<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário admin
        $this->adminUser = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com'
        ]);

        // Criar usuário regular
        $this->regularUser = User::factory()->regular()->create([
            'name' => 'Regular User',
            'email' => 'user@test.com'
        ]);
    }

    #[Test]
    public function admin_user_can_access_admin_dashboard()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_user_can_access_admin_users()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function regular_user_cannot_access_admin_dashboard()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    #[Test]
    public function regular_user_cannot_access_admin_users()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_access_admin_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_admin_users()
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_middleware_protects_all_admin_routes()
    {
        // Testar como usuário regular
        $this->actingAs($this->regularUser);

        // Lista de rotas admin que devem ser protegidas
        $protectedRoutes = [
            'admin.dashboard',
            'admin.users.index',
            'admin.users.create',
        ];

        foreach ($protectedRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(403, "Route {$routeName} should be protected");
        }
    }

    #[Test]
    public function admin_middleware_allows_admin_access()
    {
        // Testar como usuário admin
        $this->actingAs($this->adminUser);

        // Lista de rotas admin que devem ser acessíveis
        $accessibleRoutes = [
            'admin.dashboard',
            'admin.users.index',
            'admin.users.create',
        ];

        foreach ($accessibleRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200, "Route {$routeName} should be accessible to admin");
        }
    }

    #[Test]
    public function guest_is_redirected_to_login_for_admin_routes()
    {
        // Lista de rotas admin que devem redirecionar para login
        $protectedRoutes = [
            'admin.dashboard',
            'admin.users.index',
            'admin.users.create',
        ];

        foreach ($protectedRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertRedirect(route('login'), "Route {$routeName} should redirect to login");
        }
    }
}