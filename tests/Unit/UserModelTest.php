<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_be_created_with_factory()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email
        ]);
    }

    #[Test]
    public function user_has_fillable_attributes()
    {
        $user = new User();
        
        $expectedFillable = [
            'name',
            'email',
            'password',
            'is_admin'
        ];

        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    #[Test]
    public function user_has_hidden_attributes()
    {
        $user = new User();
        
        $expectedHidden = [
            'password',
            'remember_token'
        ];

        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    #[Test]
    public function user_has_casts()
    {
        $user = new User();
        
        $expectedCasts = [
            'id' => 'int',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean'
        ];

        $this->assertEquals($expectedCasts, $user->getCasts());
    }

    #[Test]
    public function user_can_be_admin()
    {
        $adminUser = User::factory()->create([
            'is_admin' => true
        ]);

        $this->assertTrue($adminUser->is_admin);
    }

    #[Test]
    public function user_can_be_regular_user()
    {
        $regularUser = User::factory()->create([
            'is_admin' => false
        ]);

        $this->assertFalse($regularUser->is_admin);
    }

    #[Test]
    public function user_password_is_hashed_when_created()
    {
        $user = User::factory()->create([
            'password' => 'plaintext-password'
        ]);

        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(Hash::check('plaintext-password', $user->password));
    }

    #[Test]
    public function user_email_must_be_unique()
    {
        $email = 'test@example.com';
        
        User::factory()->create(['email' => $email]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => $email]);
    }

    #[Test]
    public function user_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
    }

    #[Test]
    public function user_email_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Test User',
            'password' => Hash::make('password')
        ]);
    }

    #[Test]
    public function user_password_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    #[Test]
    public function user_is_admin_defaults_to_false()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->is_admin);
    }

    #[Test]
    public function user_can_update_attributes()
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com'
        ]);

        $user->update([
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);

        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@example.com', $user->email);
    }

    #[Test]
    public function user_can_update_admin_status()
    {
        $user = User::factory()->create([
            'is_admin' => false
        ]);

        $user->update(['is_admin' => true]);

        $this->assertTrue($user->is_admin);
    }

    #[Test]
    public function user_can_update_password()
    {
        $user = User::factory()->create();
        $originalPassword = $user->password;

        $user->update([
            'password' => Hash::make('new-password')
        ]);

        $this->assertNotEquals($originalPassword, $user->password);
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    #[Test]
    public function user_timestamps_are_set()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    #[Test]
    public function user_can_be_deleted()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertDatabaseMissing('users', [
            'id' => $userId
        ]);
    }

    #[Test]
    public function user_factory_creates_valid_data()
    {
        $user = User::factory()->make();

        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
        $this->assertIsBool($user->is_admin);
        $this->assertStringContainsString('@', $user->email);
    }

    #[Test]
    public function user_factory_can_create_admin()
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->is_admin);
    }

    #[Test]
    public function user_factory_can_create_regular_user()
    {
        $user = User::factory()->regular()->create();

        $this->assertFalse($user->is_admin);
    }

    #[Test]
    public function user_to_array_excludes_hidden_fields()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
        $this->assertArrayHasKey('name', $userArray);
        $this->assertArrayHasKey('email', $userArray);
        $this->assertArrayHasKey('is_admin', $userArray);
    }
}