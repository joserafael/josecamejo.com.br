<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class BlogSubcategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected BlogCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
        $this->category = BlogCategory::factory()->create();
    }

    #[Test]
    public function admin_can_view_subcategories_index()
    {
        $this->actingAs($this->adminUser);

        BlogSubcategory::factory()->count(3)->create();

        $response = $this->get(route('admin.blog-subcategories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.subcategories.index');
        $response->assertViewHas('subcategories');
        $response->assertViewHas('pageTitle', 'Gerenciar Subcategorias do Blog');
    }

    #[Test]
    public function non_admin_cannot_access_subcategories_index()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.blog-subcategories.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_search_subcategories()
    {
        $this->actingAs($this->adminUser);

        BlogSubcategory::factory()->create(['name' => 'Laravel Subcategory']);
        BlogSubcategory::factory()->create(['name' => 'PHP Subcategory']);

        $response = $this->get(route('admin.blog-subcategories.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Subcategory');
        $response->assertDontSee('PHP Subcategory');
    }

    #[Test]
    public function admin_can_filter_subcategories_by_status()
    {
        $this->actingAs($this->adminUser);

        BlogSubcategory::factory()->active()->create(['name' => 'Active Subcategory']);
        BlogSubcategory::factory()->inactive()->create(['name' => 'Inactive Subcategory']);

        $response = $this->get(route('admin.blog-subcategories.index', ['status' => 'active']));

        $response->assertStatus(200);
        $response->assertSee('Active Subcategory');
        $response->assertDontSee('Inactive Subcategory');
    }

    #[Test]
    public function admin_can_filter_subcategories_by_category()
    {
        $this->actingAs($this->adminUser);

        $category1 = BlogCategory::factory()->create(['name' => 'Category 1']);
        $category2 = BlogCategory::factory()->create(['name' => 'Category 2']);

        BlogSubcategory::factory()->forCategory($category1)->create(['name' => 'Subcategory 1']);
        BlogSubcategory::factory()->forCategory($category2)->create(['name' => 'Subcategory 2']);

        $response = $this->get(route('admin.blog-subcategories.index', ['category_id' => $category1->id]));

        $response->assertStatus(200);
        $response->assertSee('Subcategory 1');
        $response->assertDontSee('Subcategory 2');
    }

    #[Test]
    public function admin_can_view_create_subcategory_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.blog-subcategories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.subcategories.create');
        $response->assertViewHas('pageTitle', 'Nova Subcategoria do Blog');
        $response->assertViewHas('categories');
    }

    #[Test]
    public function non_admin_cannot_access_create_subcategory_form()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.blog-subcategories.create'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_store_new_subcategory()
    {
        $this->actingAs($this->adminUser);

        $subcategoryData = [
            'blog_category_id' => $this->category->id,
            'name' => 'Test Subcategory',
            'description' => 'Test subcategory description',
            'language' => 'pt',
            'is_active' => '1',
            'sort_order' => '10'
        ];

        $response = $this->post(route('admin.blog-subcategories.store'), $subcategoryData);

        $response->assertRedirect(route('admin.blog-subcategories.index'));
        $response->assertSessionHas('success', 'Subcategoria criada com sucesso!');

        $this->assertDatabaseHas('blog_subcategories', [
            'blog_category_id' => $this->category->id,
            'name' => 'Test Subcategory',
            'slug' => 'test-subcategory',
            'description' => 'Test subcategory description',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 10
        ]);
    }

    #[Test]
    public function admin_cannot_store_subcategory_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        $invalidData = [
            'blog_category_id' => 999, // Non-existent category
            'name' => '', // Required field
            'language' => 'invalid', // Invalid language
            'sort_order' => -1 // Invalid sort order
        ];

        $response = $this->post(route('admin.blog-subcategories.store'), $invalidData);

        $response->assertSessionHasErrors(['blog_category_id', 'name', 'language', 'sort_order']);
        $this->assertDatabaseCount('blog_subcategories', 0);
    }

    #[Test]
    public function admin_can_view_subcategory_details()
    {
        $this->actingAs($this->adminUser);

        $subcategory = BlogSubcategory::factory()->create(['name' => 'Test Subcategory']);

        $response = $this->get(route('admin.blog-subcategories.show', $subcategory));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.subcategories.show');
        $response->assertViewHas('subcategory', $subcategory);
        $response->assertSee('Test Subcategory');
    }

    #[Test]
    public function admin_can_view_edit_subcategory_form()
    {
        $this->actingAs($this->adminUser);

        $subcategory = BlogSubcategory::factory()->create(['name' => 'Test Subcategory']);

        $response = $this->get(route('admin.blog-subcategories.edit', $subcategory));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.subcategories.edit');
        $response->assertViewHas('subcategory', $subcategory);
        $response->assertViewHas('categories');
        $response->assertSee('Test Subcategory');
    }

    #[Test]
    public function admin_can_update_subcategory()
    {
        $this->actingAs($this->adminUser);

        $newCategory = BlogCategory::factory()->create();
        $subcategory = BlogSubcategory::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original description'
        ]);

        $updateData = [
            'blog_category_id' => $newCategory->id,
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'language' => 'en',
            'is_active' => '1',
            'sort_order' => '20'
        ];

        $response = $this->put(route('admin.blog-subcategories.update', $subcategory), $updateData);

        $response->assertRedirect(route('admin.blog-subcategories.index'));
        $response->assertSessionHas('success', 'Subcategoria atualizada com sucesso!');

        $subcategory->refresh();
        $this->assertEquals($newCategory->id, $subcategory->blog_category_id);
        $this->assertEquals('Updated Name', $subcategory->name);
        $this->assertEquals('updated-name', $subcategory->slug);
        $this->assertEquals('Updated description', $subcategory->description);
        $this->assertEquals('en', $subcategory->language);
        $this->assertTrue($subcategory->is_active);
        $this->assertEquals(20, $subcategory->sort_order);
    }

    #[Test]
    public function admin_cannot_update_subcategory_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        $subcategory = BlogSubcategory::factory()->create();

        $invalidData = [
            'blog_category_id' => 999, // Non-existent category
            'name' => '', // Required field
            'language' => 'invalid', // Invalid language
        ];

        $response = $this->put(route('admin.blog-subcategories.update', $subcategory), $invalidData);

        $response->assertSessionHasErrors(['blog_category_id', 'name', 'language']);
    }

    #[Test]
    public function updating_subcategory_name_generates_new_slug()
    {
        $this->actingAs($this->adminUser);

        $subcategory = BlogSubcategory::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-name'
        ]);

        $updateData = [
            'blog_category_id' => $subcategory->blog_category_id,
            'name' => 'New Name',
            'description' => $subcategory->description,
            'language' => $subcategory->language,
            'is_active' => '1',
            'sort_order' => $subcategory->sort_order
        ];

        $this->put(route('admin.blog-subcategories.update', $subcategory), $updateData);

        $subcategory->refresh();
        $this->assertEquals('New Name', $subcategory->name);
        $this->assertEquals('new-name', $subcategory->slug);
    }

    #[Test]
    public function admin_can_delete_subcategory()
    {
        $this->actingAs($this->adminUser);

        $subcategory = BlogSubcategory::factory()->create();

        $response = $this->delete(route('admin.blog-subcategories.destroy', $subcategory));

        $response->assertRedirect(route('admin.blog-subcategories.index'));
        $response->assertSessionHas('success', 'Subcategoria excluída com sucesso!');

        $this->assertDatabaseMissing('blog_subcategories', ['id' => $subcategory->id]);
    }

    #[Test]
    public function non_admin_cannot_delete_subcategory()
    {
        $this->actingAs($this->regularUser);

        $subcategory = BlogSubcategory::factory()->create();

        $response = $this->delete(route('admin.blog-subcategories.destroy', $subcategory));

        $response->assertStatus(403);
        $this->assertDatabaseHas('blog_subcategories', ['id' => $subcategory->id]);
    }

    #[Test]
    public function subcategories_are_paginated()
    {
        $this->actingAs($this->adminUser);

        BlogSubcategory::factory()->count(20)->create();

        $response = $this->get(route('admin.blog-subcategories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('subcategories');
        
        $subcategories = $response->viewData('subcategories');
        $this->assertEquals(15, $subcategories->perPage());
        $this->assertTrue($subcategories->hasPages());
    }

    #[Test]
    public function subcategories_are_ordered_by_sort_order()
    {
        $this->actingAs($this->adminUser);

        BlogSubcategory::factory()->create(['name' => 'Third', 'sort_order' => 30]);
        BlogSubcategory::factory()->create(['name' => 'First', 'sort_order' => 10]);
        BlogSubcategory::factory()->create(['name' => 'Second', 'sort_order' => 20]);

        $response = $this->get(route('admin.blog-subcategories.index'));

        $response->assertStatus(200);
        
        $subcategories = $response->viewData('subcategories');
        $this->assertEquals('First', $subcategories->first()->name);
        $this->assertEquals('Second', $subcategories->get(1)->name);
        $this->assertEquals('Third', $subcategories->last()->name);
    }

    #[Test]
    public function subcategory_belongs_to_category()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create(['name' => 'Test Category']);
        $subcategory = BlogSubcategory::factory()->forCategory($category)->create(['name' => 'Test Subcategory']);

        $response = $this->get(route('admin.blog-subcategories.show', $subcategory));

        $response->assertStatus(200);
        $response->assertSee('Test Category');
        $response->assertSee('Test Subcategory');
    }

    #[Test]
    public function checkbox_is_active_works_correctly()
    {
        $this->actingAs($this->adminUser);

        // Test creating active subcategory
        $activeData = [
            'blog_category_id' => $this->category->id,
            'name' => 'Active Subcategory',
            'language' => 'pt',
            'is_active' => '1'
        ];

        $this->post(route('admin.blog-subcategories.store'), $activeData);
        $this->assertDatabaseHas('blog_subcategories', ['name' => 'Active Subcategory', 'is_active' => true]);

        // Test creating inactive subcategory (checkbox not checked)
        $inactiveData = [
            'blog_category_id' => $this->category->id,
            'name' => 'Inactive Subcategory',
            'language' => 'pt'
            // is_active not included (checkbox not checked)
        ];

        $this->post(route('admin.blog-subcategories.store'), $inactiveData);
        $this->assertDatabaseHas('blog_subcategories', ['name' => 'Inactive Subcategory', 'is_active' => false]);
    }

    #[Test]
    public function subcategory_can_be_created_with_category_preselected()
    {
        $this->actingAs($this->adminUser);

        $category = BlogCategory::factory()->create(['name' => 'Preselected Category']);

        $response = $this->get(route('admin.blog-subcategories.create', ['category_id' => $category->id]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedCategoryId', $category->id);
    }
}