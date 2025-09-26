<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class BlogSubcategoryModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_blog_subcategory_with_all_fields()
    {
        $category = BlogCategory::factory()->create();
        
        $subcategoryData = [
            'blog_category_id' => $category->id,
            'name' => 'Test Subcategory',
            'slug' => 'test-subcategory',
            'description' => 'Test subcategory description',
            'language' => 'pt',
            'is_active' => true,
            'sort_order' => 10
        ];

        $subcategory = BlogSubcategory::create($subcategoryData);

        $this->assertInstanceOf(BlogSubcategory::class, $subcategory);
        $this->assertEquals($subcategoryData['blog_category_id'], $subcategory->blog_category_id);
        $this->assertEquals($subcategoryData['name'], $subcategory->name);
        $this->assertEquals($subcategoryData['slug'], $subcategory->slug);
        $this->assertEquals($subcategoryData['description'], $subcategory->description);
        $this->assertEquals($subcategoryData['language'], $subcategory->language);
        $this->assertTrue($subcategory->is_active);
        $this->assertEquals($subcategoryData['sort_order'], $subcategory->sort_order);
    }

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $subcategory = new BlogSubcategory();
        $expected = [
            'blog_category_id',
            'name',
            'slug',
            'description',
            'language',
            'is_active',
            'sort_order'
        ];

        $this->assertEquals($expected, $subcategory->getFillable());
    }

    #[Test]
    public function it_has_correct_casts()
    {
        $subcategory = new BlogSubcategory();
        $expected = [
            'id' => 'int',
            'blog_category_id' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ];

        $this->assertEquals($expected, $subcategory->getCasts());
    }

    #[Test]
    public function it_belongs_to_category()
    {
        $category = BlogCategory::factory()->create(['name' => 'Parent Category']);
        $subcategory = BlogSubcategory::factory()->forCategory($category)->create();

        $this->assertInstanceOf(BlogCategory::class, $subcategory->category);
        $this->assertEquals($category->id, $subcategory->category->id);
        $this->assertEquals('Parent Category', $subcategory->category->name);
    }

    #[Test]
    public function it_can_scope_active_subcategories()
    {
        BlogSubcategory::factory()->active()->create(['name' => 'Active Subcategory']);
        BlogSubcategory::factory()->inactive()->create(['name' => 'Inactive Subcategory']);

        $activeSubcategories = BlogSubcategory::active()->get();

        $this->assertCount(1, $activeSubcategories);
        $this->assertEquals('Active Subcategory', $activeSubcategories->first()->name);
        $this->assertTrue($activeSubcategories->first()->is_active);
    }

    #[Test]
    public function it_can_scope_inactive_subcategories()
    {
        BlogSubcategory::factory()->active()->create(['name' => 'Active Subcategory']);
        BlogSubcategory::factory()->inactive()->create(['name' => 'Inactive Subcategory']);

        $inactiveSubcategories = BlogSubcategory::inactive()->get();

        $this->assertCount(1, $inactiveSubcategories);
        $this->assertEquals('Inactive Subcategory', $inactiveSubcategories->first()->name);
        $this->assertFalse($inactiveSubcategories->first()->is_active);
    }

    #[Test]
    public function it_can_scope_ordered_subcategories()
    {
        BlogSubcategory::factory()->create(['name' => 'Third', 'sort_order' => 30]);
        BlogSubcategory::factory()->create(['name' => 'First', 'sort_order' => 10]);
        BlogSubcategory::factory()->create(['name' => 'Second', 'sort_order' => 20]);

        $orderedSubcategories = BlogSubcategory::ordered()->get();

        $this->assertEquals('First', $orderedSubcategories->first()->name);
        $this->assertEquals('Second', $orderedSubcategories->get(1)->name);
        $this->assertEquals('Third', $orderedSubcategories->last()->name);
    }

    #[Test]
    public function it_can_scope_by_language()
    {
        BlogSubcategory::factory()->language('pt')->create(['name' => 'Portuguese Subcategory']);
        BlogSubcategory::factory()->language('en')->create(['name' => 'English Subcategory']);
        BlogSubcategory::factory()->language('es')->create(['name' => 'Spanish Subcategory']);

        $portugueseSubcategories = BlogSubcategory::byLanguage('pt')->get();
        $englishSubcategories = BlogSubcategory::byLanguage('en')->get();

        $this->assertCount(1, $portugueseSubcategories);
        $this->assertCount(1, $englishSubcategories);
        $this->assertEquals('Portuguese Subcategory', $portugueseSubcategories->first()->name);
        $this->assertEquals('English Subcategory', $englishSubcategories->first()->name);
    }

    #[Test]
    public function it_generates_unique_slug()
    {
        $slug1 = BlogSubcategory::generateSlug('Test Subcategory');
        BlogSubcategory::factory()->create(['slug' => $slug1]);
        
        $slug2 = BlogSubcategory::generateSlug('Test Subcategory');

        $this->assertEquals('test-subcategory', $slug1);
        $this->assertEquals('test-subcategory-1', $slug2);
    }

    #[Test]
    public function it_generates_multiple_unique_slugs()
    {
        BlogSubcategory::factory()->create(['slug' => 'test-subcategory']);
        BlogSubcategory::factory()->create(['slug' => 'test-subcategory-1']);
        
        $slug = BlogSubcategory::generateSlug('Test Subcategory');

        $this->assertEquals('test-subcategory-2', $slug);
    }

    #[Test]
    public function get_name_method_returns_name_attribute()
    {
        $subcategory = BlogSubcategory::factory()->create(['name' => 'Test Subcategory Name']);

        $this->assertEquals('Test Subcategory Name', $subcategory->getName());
    }

    #[Test]
    public function it_can_be_created_using_factory()
    {
        $subcategory = BlogSubcategory::factory()->create();

        $this->assertInstanceOf(BlogSubcategory::class, $subcategory);
        $this->assertNotEmpty($subcategory->name);
        $this->assertNotEmpty($subcategory->slug);
        $this->assertIsInt($subcategory->blog_category_id);
        $this->assertContains($subcategory->language, ['pt', 'en', 'es']);
        $this->assertIsBool($subcategory->is_active);
        $this->assertIsInt($subcategory->sort_order);
    }

    #[Test]
    public function it_can_be_created_with_factory_states()
    {
        $category = BlogCategory::factory()->create();
        
        $activeSubcategory = BlogSubcategory::factory()->active()->create();
        $inactiveSubcategory = BlogSubcategory::factory()->inactive()->create();
        $portugueseSubcategory = BlogSubcategory::factory()->language('pt')->create();
        $categorySubcategory = BlogSubcategory::factory()->forCategory($category)->create();

        $this->assertTrue($activeSubcategory->is_active);
        $this->assertFalse($inactiveSubcategory->is_active);
        $this->assertEquals('pt', $portugueseSubcategory->language);
        $this->assertEquals($category->id, $categorySubcategory->blog_category_id);
    }

    #[Test]
    public function it_can_scope_by_category()
    {
        $category1 = BlogCategory::factory()->create(['name' => 'Category 1']);
        $category2 = BlogCategory::factory()->create(['name' => 'Category 2']);
        
        BlogSubcategory::factory()->forCategory($category1)->create(['name' => 'Subcategory 1']);
        BlogSubcategory::factory()->forCategory($category2)->create(['name' => 'Subcategory 2']);

        $category1Subcategories = BlogSubcategory::byCategory($category1->id)->get();
        $category2Subcategories = BlogSubcategory::byCategory($category2->id)->get();

        $this->assertCount(1, $category1Subcategories);
        $this->assertCount(1, $category2Subcategories);
        $this->assertEquals('Subcategory 1', $category1Subcategories->first()->name);
        $this->assertEquals('Subcategory 2', $category2Subcategories->first()->name);
    }
}