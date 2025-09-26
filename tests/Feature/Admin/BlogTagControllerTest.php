<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\BlogTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class BlogTagControllerTest extends TestCase
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
    public function admin_can_view_tags_index()
    {
        $this->actingAs($this->adminUser);

        BlogTag::factory()->count(3)->create();

        $response = $this->get(route('admin.blog-tags.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.tags.index');
        $response->assertViewHas('tags');
        $response->assertViewHas('pageTitle', 'Gerenciar Tags do Blog');
    }

    #[Test]
    public function non_admin_cannot_access_tags_index()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.blog-tags.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_search_tags()
    {
        $this->actingAs($this->adminUser);

        BlogTag::factory()->create(['name' => 'Laravel Tag']);
        BlogTag::factory()->create(['name' => 'PHP Tag']);

        $response = $this->get(route('admin.blog-tags.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Tag');
        $response->assertDontSee('PHP Tag');
    }

    #[Test]
    public function admin_can_filter_tags_by_status()
    {
        $this->actingAs($this->adminUser);

        BlogTag::factory()->active()->create(['name' => 'Active Tag']);
        BlogTag::factory()->inactive()->create(['name' => 'Inactive Tag']);

        $response = $this->get(route('admin.blog-tags.index', ['status' => 'active']));

        $response->assertStatus(200);
        $response->assertSee('Active Tag');
        $response->assertDontSee('Inactive Tag');
    }

    #[Test]
    public function admin_can_filter_tags_by_language()
    {
        $this->actingAs($this->adminUser);

        BlogTag::factory()->portuguese()->create(['name' => 'Tag Português']);
        BlogTag::factory()->english()->create(['name' => 'English Tag']);

        $response = $this->get(route('admin.blog-tags.index', ['language' => 'pt']));

        $response->assertStatus(200);
        $response->assertSee('Tag Português');
        $response->assertDontSee('English Tag');
    }

    #[Test]
    public function admin_can_view_create_tag_form()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.blog-tags.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.tags.create');
        $response->assertViewHas('pageTitle', 'Nova Tag do Blog');
    }

    #[Test]
    public function non_admin_cannot_access_create_tag_form()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('admin.blog-tags.create'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_store_new_tag()
    {
        $this->actingAs($this->adminUser);

        $tagData = [
            'name' => 'Test Tag',
            'description' => 'Test tag description',
            'language' => 'pt',
            'is_active' => '1',
            'color' => '#FF5733'
        ];

        $response = $this->post(route('admin.blog-tags.store'), $tagData);

        $response->assertRedirect(route('admin.blog-tags.index'));
        $response->assertSessionHas('success', 'Tag criada com sucesso!');

        $this->assertDatabaseHas('blog_tags', [
            'name' => 'Test Tag',
            'slug' => 'test-tag',
            'description' => 'Test tag description',
            'language' => 'pt',
            'is_active' => true,
            'color' => '#FF5733'
        ]);
    }

    #[Test]
    public function admin_can_store_tag_without_color()
    {
        $this->actingAs($this->adminUser);

        $tagData = [
            'name' => 'Test Tag No Color',
            'description' => 'Test tag description',
            'language' => 'pt',
            'is_active' => '1'
            // color not provided - should generate random color
        ];

        $response = $this->post(route('admin.blog-tags.store'), $tagData);

        $response->assertRedirect(route('admin.blog-tags.index'));
        $response->assertSessionHas('success', 'Tag criada com sucesso!');

        $tag = BlogTag::where('name', 'Test Tag No Color')->first();
        $this->assertNotNull($tag);
        $this->assertNotNull($tag->color);
        $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $tag->color);
    }

    #[Test]
    public function admin_cannot_store_tag_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        $invalidData = [
            'name' => '', // Required field
            'language' => 'invalid', // Invalid language
            'color' => 'invalid-color' // Invalid color format
        ];

        $response = $this->post(route('admin.blog-tags.store'), $invalidData);

        $response->assertSessionHasErrors(['name', 'language', 'color']);
        $this->assertDatabaseCount('blog_tags', 0);
    }

    #[Test]
    public function admin_can_view_tag_details()
    {
        $this->actingAs($this->adminUser);

        $tag = BlogTag::factory()->create(['name' => 'Test Tag']);

        $response = $this->get(route('admin.blog-tags.show', $tag));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.tags.show');
        $response->assertViewHas('tag', $tag);
        $response->assertSee('Test Tag');
    }

    #[Test]
    public function admin_can_view_edit_tag_form()
    {
        $this->actingAs($this->adminUser);

        $tag = BlogTag::factory()->create(['name' => 'Test Tag']);

        $response = $this->get(route('admin.blog-tags.edit', $tag));

        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.tags.edit');
        $response->assertViewHas('tag', $tag);
        $response->assertSee('Test Tag');
    }

    #[Test]
    public function admin_can_update_tag()
    {
        $this->actingAs($this->adminUser);

        $tag = BlogTag::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original description',
            'color' => '#000000'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'language' => 'en',
            'is_active' => '1',
            'color' => '#FFFFFF'
        ];

        $response = $this->put(route('admin.blog-tags.update', $tag), $updateData);

        $response->assertRedirect(route('admin.blog-tags.index'));
        $response->assertSessionHas('success', 'Tag atualizada com sucesso!');

        $tag->refresh();
        $this->assertEquals('Updated Name', $tag->name);
        $this->assertEquals('updated-name', $tag->slug);
        $this->assertEquals('Updated description', $tag->description);
        $this->assertEquals('en', $tag->language);
        $this->assertTrue($tag->is_active);
        $this->assertEquals('#FFFFFF', $tag->color);
    }

    #[Test]
    public function admin_cannot_update_tag_with_invalid_data()
    {
        $this->actingAs($this->adminUser);

        $tag = BlogTag::factory()->create();

        $invalidData = [
            'name' => '', // Required field
            'language' => 'invalid', // Invalid language
            'color' => 'invalid-color' // Invalid color format
        ];

        $response = $this->put(route('admin.blog-tags.update', $tag), $invalidData);

        $response->assertSessionHasErrors(['name', 'language', 'color']);
    }

    #[Test]
    public function updating_tag_name_generates_new_slug()
    {
        $this->actingAs($this->adminUser);

        $tag = BlogTag::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-name'
        ]);

        $updateData = [
            'name' => 'New Name',
            'description' => $tag->description,
            'language' => $tag->language,
            'is_active' => '1',
            'color' => $tag->color
        ];

        $this->put(route('admin.blog-tags.update', $tag), $updateData);

        $tag->refresh();
        $this->assertEquals('New Name', $tag->name);
        $this->assertEquals('new-name', $tag->slug);
    }

    #[Test]
    public function admin_can_delete_tag()
    {
        $this->actingAs($this->adminUser);

        $tag = BlogTag::factory()->create();

        $response = $this->delete(route('admin.blog-tags.destroy', $tag));

        $response->assertRedirect(route('admin.blog-tags.index'));
        $response->assertSessionHas('success', 'Tag excluída com sucesso!');

        $this->assertDatabaseMissing('blog_tags', ['id' => $tag->id]);
    }

    #[Test]
    public function non_admin_cannot_delete_tag()
    {
        $this->actingAs($this->regularUser);

        $tag = BlogTag::factory()->create();

        $response = $this->delete(route('admin.blog-tags.destroy', $tag));

        $response->assertStatus(403);
        $this->assertDatabaseHas('blog_tags', ['id' => $tag->id]);
    }

    #[Test]
    public function tags_are_paginated()
    {
        $this->actingAs($this->adminUser);

        BlogTag::factory()->count(20)->create();

        $response = $this->get(route('admin.blog-tags.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tags');
        
        $tags = $response->viewData('tags');
        $this->assertEquals(15, $tags->perPage());
        $this->assertTrue($tags->hasPages());
    }

    #[Test]
    public function tags_are_ordered_alphabetically()
    {
        $this->actingAs($this->adminUser);

        BlogTag::factory()->create(['name' => 'Zebra Tag']);
        BlogTag::factory()->create(['name' => 'Alpha Tag']);
        BlogTag::factory()->create(['name' => 'Beta Tag']);

        $response = $this->get(route('admin.blog-tags.index'));

        $response->assertStatus(200);
        
        $tags = $response->viewData('tags');
        $this->assertEquals('Alpha Tag', $tags->first()->name);
        $this->assertEquals('Beta Tag', $tags->get(1)->name);
        $this->assertEquals('Zebra Tag', $tags->last()->name);
    }

    #[Test]
    public function checkbox_is_active_works_correctly()
    {
        $this->actingAs($this->adminUser);

        // Test creating active tag
        $activeData = [
            'name' => 'Active Tag',
            'language' => 'pt',
            'is_active' => '1',
            'color' => '#FF0000'
        ];

        $this->post(route('admin.blog-tags.store'), $activeData);
        $this->assertDatabaseHas('blog_tags', ['name' => 'Active Tag', 'is_active' => true]);

        // Test creating inactive tag (checkbox not checked)
        $inactiveData = [
            'name' => 'Inactive Tag',
            'language' => 'pt',
            'color' => '#00FF00'
            // is_active not included (checkbox not checked)
        ];

        $this->post(route('admin.blog-tags.store'), $inactiveData);
        $this->assertDatabaseHas('blog_tags', ['name' => 'Inactive Tag', 'is_active' => false]);
    }

    #[Test]
    public function tag_color_validation_accepts_valid_hex_colors()
    {
        $this->actingAs($this->adminUser);

        $validColors = ['#FF0000', '#00FF00', '#0000FF', '#FFFFFF', '#000000', '#123ABC'];

        foreach ($validColors as $color) {
            $tagData = [
                'name' => 'Test Tag ' . $color,
                'language' => 'pt',
                'color' => $color
            ];

            $response = $this->post(route('admin.blog-tags.store'), $tagData);
            $response->assertRedirect(route('admin.blog-tags.index'));
            $this->assertDatabaseHas('blog_tags', ['color' => $color]);
        }
    }

    #[Test]
    public function tag_color_validation_rejects_invalid_colors()
    {
        $this->actingAs($this->adminUser);

        $invalidColors = ['red', 'FF0000', '#GG0000', '#FF00', '#FF00000', 'rgb(255,0,0)'];

        foreach ($invalidColors as $color) {
            $tagData = [
                'name' => 'Test Tag',
                'language' => 'pt',
                'color' => $color
            ];

            $response = $this->post(route('admin.blog-tags.store'), $tagData);
            $response->assertSessionHasErrors(['color']);
        }
    }

    #[Test]
    public function tag_displays_color_in_index()
    {
        $this->actingAs($this->adminUser);

        $tag = BlogTag::factory()->withColor('#FF5733')->create(['name' => 'Colored Tag']);

        $response = $this->get(route('admin.blog-tags.index'));

        $response->assertStatus(200);
        $response->assertSee('Colored Tag');
        $response->assertSee('#FF5733');
    }


}