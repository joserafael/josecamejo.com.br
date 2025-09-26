<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class BlogCategoryModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_blog_category_with_all_fields()
    {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 10
        ];

        $category = BlogCategory::create($categoryData);

        $this->assertInstanceOf(BlogCategory::class, $category);
        $this->assertEquals($categoryData['name'], $category->name);
        $this->assertEquals($categoryData['slug'], $category->slug);
        $this->assertEquals($categoryData['description'], $category->description);
        $this->assertEquals($categoryData['language'], $category->language);
        $this->assertTrue($category->is_active);
        $this->assertEquals($categoryData['sort_order'], $category->sort_order);
    }

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $category = new BlogCategory();
        $expected = [
            'name',
            'slug',
            'description',
            'language',
            'is_active',
            'sort_order'
        ];

        $this->assertEquals($expected, $category->getFillable());
    }

    #[Test]
    public function it_has_correct_casts()
    {
        $category = new BlogCategory();
        $expected = [
            'id' => 'int',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ];

        $this->assertEquals($expected, $category->getCasts());
    }

    #[Test]
    public function it_has_subcategories_relationship()
    {
        $category = BlogCategory::factory()->create();
        $subcategory = BlogSubcategory::factory()->forCategory($category)->create();

        $this->assertTrue($category->subcategories()->exists());
        $this->assertInstanceOf(BlogSubcategory::class, $category->subcategories->first());
        $this->assertEquals($subcategory->id, $category->subcategories->first()->id);
    }

    #[Test]
    public function it_can_scope_active_categories()
    {
        BlogCategory::factory()->active()->create(['name' => 'Active Category']);
        BlogCategory::factory()->inactive()->create(['name' => 'Inactive Category']);

        $activeCategories = BlogCategory::active()->get();

        $this->assertCount(1, $activeCategories);
        $this->assertEquals('Active Category', $activeCategories->first()->name);
        $this->assertTrue($activeCategories->first()->is_active);
    }

    #[Test]
    public function it_can_scope_inactive_categories()
    {
        BlogCategory::factory()->active()->create(['name' => 'Active Category']);
        BlogCategory::factory()->inactive()->create(['name' => 'Inactive Category']);

        $inactiveCategories = BlogCategory::inactive()->get();

        $this->assertCount(1, $inactiveCategories);
        $this->assertEquals('Inactive Category', $inactiveCategories->first()->name);
        $this->assertFalse($inactiveCategories->first()->is_active);
    }

    #[Test]
    public function it_can_scope_ordered_categories()
    {
        BlogCategory::factory()->create(['name' => 'Third', 'sort_order' => 30]);
        BlogCategory::factory()->create(['name' => 'First', 'sort_order' => 10]);
        BlogCategory::factory()->create(['name' => 'Second', 'sort_order' => 20]);

        $orderedCategories = BlogCategory::ordered()->get();

        $this->assertEquals('First', $orderedCategories->first()->name);
        $this->assertEquals('Second', $orderedCategories->get(1)->name);
        $this->assertEquals('Third', $orderedCategories->last()->name);
    }

    #[Test]
    public function it_can_scope_by_language()
    {
        BlogCategory::factory()->language('pt')->create(['name' => 'Portuguese Category']);
        BlogCategory::factory()->language('en')->create(['name' => 'English Category']);
        BlogCategory::factory()->language('es')->create(['name' => 'Spanish Category']);

        $portugueseCategories = BlogCategory::byLanguage('pt')->get();
        $englishCategories = BlogCategory::byLanguage('en')->get();

        $this->assertCount(1, $portugueseCategories);
        $this->assertCount(1, $englishCategories);
        $this->assertEquals('Portuguese Category', $portugueseCategories->first()->name);
        $this->assertEquals('English Category', $englishCategories->first()->name);
    }

    #[Test]
    public function it_generates_unique_slug()
    {
        $slug1 = BlogCategory::generateSlug('Test Category');
        BlogCategory::factory()->create(['slug' => $slug1]);
        
        $slug2 = BlogCategory::generateSlug('Test Category');

        $this->assertEquals('test-category', $slug1);
        $this->assertEquals('test-category-1', $slug2);
    }

    #[Test]
    public function it_generates_multiple_unique_slugs()
    {
        BlogCategory::factory()->create(['slug' => 'test-category']);
        BlogCategory::factory()->create(['slug' => 'test-category-1']);
        
        $slug = BlogCategory::generateSlug('Test Category');

        $this->assertEquals('test-category-2', $slug);
    }

    #[Test]
    public function get_name_method_returns_name_attribute()
    {
        $category = BlogCategory::factory()->create(['name' => 'Test Category Name']);

        $this->assertEquals('Test Category Name', $category->getName());
    }

    #[Test]
    public function it_can_be_created_using_factory()
    {
        $category = BlogCategory::factory()->create();

        $this->assertInstanceOf(BlogCategory::class, $category);
        $this->assertNotEmpty($category->name);
        $this->assertNotEmpty($category->slug);
        $this->assertContains($category->language, ['pt', 'en', 'es']);
        $this->assertIsBool($category->is_active);
        $this->assertIsInt($category->sort_order);
    }

    #[Test]
    public function it_can_be_created_with_factory_states()
    {
        $activeCategory = BlogCategory::factory()->active()->create();
        $inactiveCategory = BlogCategory::factory()->inactive()->create();
        $portugueseCategory = BlogCategory::factory()->language('pt')->create();

        $this->assertTrue($activeCategory->is_active);
        $this->assertFalse($inactiveCategory->is_active);
        $this->assertEquals('pt', $portugueseCategory->language);
    }
}