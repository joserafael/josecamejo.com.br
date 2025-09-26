<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\BlogCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class BlogCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    #[Test]
    public function admin_can_view_categories_index()
    {
        $this->actingAs($this->adminUser);

        BlogCategory::factory()->count(3)->create();

        $response = $this->get(route('admin.blog-categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.categories.index');
        $response->assertViewHas('categories');
        $response->assertViewHas('pageTitle', 'Gerenciar Categorias do Blog');
    }

    #[Test]
    public function non_admin_cannot_access_categories_index()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.blog-categories.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_search_categories()
    {
        $this->actingAs($this->adminUser);

        BlogCategory::factory()->create(['name' => 'Laravel Category']);
        BlogCategory::factory()->create(['name' => 'PHP Category']);

        $response = $this->get(route('admin.blog-categories.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Category');
        $response->assertDontSee('PHP Category');
    }

    #[Test]
    public function admin_can_filter_categories_by_status()
    {
        $this->actingAs($this->adminUser);

        BlogCategory::factory()->active()->create(['name' => 'Active Category']);
        BlogCategory::factory()->inactive()->create(['name' => 'Inactive Category']);

        $response = $this->get(route('admin.blog-categories.index', ['status' => 'active']));

        $response->assertStatus(200);
        $response->assertSee('Active Category');
        $response->assertDontSee('Inactive Category');
    }

    #[Test]
    public function admin_can_view_create_category_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.blog-categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.categories.create');
        $response->assertViewHas('pageTitle', 'Nova Categoria do Blog');
    }

    #[Test]
    public function non_admin_cannot_access_create_category_form()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.blog-categories.create'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_store_new_category()
    {
        $this->actingAs($this->adminUser);

        $categoryData = [
            'name' => 'Test Category',
            'description' => 'Test category description',
            'language' => 'pt',
            'is_active' => '1',
            'sort_order' => '10'
        ];

        $response = $this->post(route('admin.blog-categories.store'), $categoryData);

        $response->assertRedirect(route('admin.blog-categories.index'));
        $response->assertSessionHas('success', 'Categoria criada com sucesso!');

        $this->assertDatabaseHas('blog_categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 10
        ]);
    }

    #[Test]
    public function admin_cannot_store_category_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        $invalidData = [
            'name' => '', // Required field
            'language' => 'invalid', // Invalid language
            'sort_order' => -1 // Invalid sort order
        ];

        $response = $this->post(route('admin.blog-categories.store'), $invalidData);

        $response->assertSessionHasErrors(['name', 'language', 'sort_order']);
        $this->assertDatabaseCount('blog_categories', 0);
    }

    #[Test]
    public function admin_can_view_category_details()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create(['name' => 'Test Category']);

        $response = $this->get(route('admin.blog-categories.show', $category));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.categories.show');
        $response->assertViewHas('category', $category);
        $response->assertSee('Test Category');
    }

    #[Test]
    public function admin_can_view_edit_category_form()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create(['name' => 'Test Category']);

        $response = $this->get(route('admin.blog-categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.categories.edit');
        $response->assertViewHas('category', $category);
        $response->assertSee('Test Category');
    }

    #[Test]
    public function admin_can_update_category()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original description'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'language' => 'en',
            'is_active' => '1',
            'sort_order' => '20'
        ];

        $response = $this->put(route('admin.blog-categories.update', $category), $updateData);

        $response->assertRedirect(route('admin.blog-categories.index'));
        $response->assertSessionHas('success', 'Categoria atualizada com sucesso!');

        $category->refresh();
        $this->assertEquals('Updated Name', $category->name);
        $this->assertEquals('updated-name', $category->slug);
        $this->assertEquals('Updated description', $category->description);
        $this->assertEquals('en', $category->language);
        $this->assertTrue($category->is_active);
        $this->assertEquals(20, $category->sort_order);
    }

    #[Test]
    public function admin_cannot_update_category_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create();

        $invalidData = [
            'name' => '', // Required field
            'language' => 'invalid', // Invalid language
        ];

        $response = $this->put(route('admin.blog-categories.update', $category), $invalidData);

        $response->assertSessionHasErrors(['name', 'language']);
    }

    #[Test]
    public function updating_category_name_generates_new_slug()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-name'
        ]);

        $updateData = [
            'name' => 'New Name',
            'description' => $category->description,
            'language' => $category->language,
            'is_active' => '1',
            'sort_order' => $category->sort_order
        ];

        $this->put(route('admin.blog-categories.update', $category), $updateData);

        $category->refresh();
        $this->assertEquals('New Name', $category->name);
        $this->assertEquals('new-name', $category->slug);
    }

    #[Test]
    public function admin_can_delete_category()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create();

        $response = $this->delete(route('admin.blog-categories.destroy', $category));

        $response->assertRedirect(route('admin.blog-categories.index'));
        $response->assertSessionHas('success', 'Categoria excluída com sucesso!');

        $this->assertDatabaseMissing('blog_categories', ['id' => $category->id]);
    }

    #[Test]
    public function non_admin_cannot_delete_category()
    {
        $this->actingAs($this->regularUser);

        $category = BlogCategory::factory()->create();

        $response = $this->delete(route('admin.blog-categories.destroy', $category));

        $response->assertStatus(403);
        $this->assertDatabaseHas('blog_categories', ['id' => $category->id]);
    }

    #[Test]
    public function categories_are_paginated()
    {
        $this->actingAs($this->adminUser);

        BlogCategory::factory()->count(20)->create();

        $response = $this->get(route('admin.blog-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
        
        $categories = $response->viewData('categories');
        $this->assertEquals(15, $categories->perPage());
        $this->assertTrue($categories->hasPages());
    }

    #[Test]
    public function categories_are_ordered_by_sort_order()
    {
        $this->actingAs($this->adminUser);

        BlogCategory::factory()->create(['name' => 'Third', 'sort_order' => 30]);
        BlogCategory::factory()->create(['name' => 'First', 'sort_order' => 10]);
        BlogCategory::factory()->create(['name' => 'Second', 'sort_order' => 20]);

        $response = $this->get(route('admin.blog-categories.index'));

        $response->assertStatus(200);
        
        $categories = $response->viewData('categories');
        $this->assertEquals('First', $categories->first()->name);
        $this->assertEquals('Second', $categories->get(1)->name);
        $this->assertEquals('Third', $categories->last()->name);
    }

    #[Test]
    public function checkbox_is_active_works_correctly()
    {
        $this->actingAs($this->adminUser);

        // Test creating active category
        $activeData = [
            'name' => 'Active Category',
            'language' => 'pt',
            'is_active' => '1'
        ];

        $this->post(route('admin.blog-categories.store'), $activeData);
        $this->assertDatabaseHas('blog_categories', ['name' => 'Active Category', 'is_active' => true]);

        // Test creating inactive category (checkbox not checked)
        $inactiveData = [
            'name' => 'Inactive Category',
            'language' => 'pt'
            // is_active not included (checkbox not checked)
        ];

        $this->post(route('admin.blog-categories.store'), $inactiveData);
        $this->assertDatabaseHas('blog_categories', ['name' => 'Inactive Category', 'is_active' => false]);
    }
}