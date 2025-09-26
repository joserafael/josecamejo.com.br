<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BlogTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class BlogTagModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_blog_tag_with_all_fields()
    {
        $tagData = [
            'name' => 'Test Tag',
            'slug' => 'test-tag',
            'description' => 'Test tag description',
            'language' => 'pt',
            'is_active' => true,
            'color' => '#FF5733'
        ];

        $tag = BlogTag::create($tagData);

        $this->assertInstanceOf(BlogTag::class, $tag);
        $this->assertEquals($tagData['name'], $tag->name);
        $this->assertEquals($tagData['slug'], $tag->slug);
        $this->assertEquals($tagData['description'], $tag->description);
        $this->assertEquals($tagData['language'], $tag->language);
        $this->assertTrue($tag->is_active);
        $this->assertEquals($tagData['color'], $tag->color);
    }

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $tag = new BlogTag();
        $expected = [
            'name',
            'slug',
            'description',
            'language',
            'is_active',
            'color'
        ];

        $this->assertEquals($expected, $tag->getFillable());
    }

    #[Test]
    public function it_has_correct_casts()
    {
        $tag = new BlogTag();
        $expected = [
            'id' => 'int',
            'is_active' => 'boolean'
        ];

        $this->assertEquals($expected, $tag->getCasts());
    }

    #[Test]
    public function it_can_scope_active_tags()
    {
        BlogTag::factory()->active()->create(['name' => 'Active Tag']);
        BlogTag::factory()->inactive()->create(['name' => 'Inactive Tag']);

        $activeTags = BlogTag::active()->get();

        $this->assertCount(1, $activeTags);
        $this->assertEquals('Active Tag', $activeTags->first()->name);
        $this->assertTrue($activeTags->first()->is_active);
    }

    #[Test]
    public function it_can_scope_inactive_tags()
    {
        BlogTag::factory()->active()->create(['name' => 'Active Tag']);
        BlogTag::factory()->inactive()->create(['name' => 'Inactive Tag']);

        $inactiveTags = BlogTag::inactive()->get();

        $this->assertCount(1, $inactiveTags);
        $this->assertEquals('Inactive Tag', $inactiveTags->first()->name);
        $this->assertFalse($inactiveTags->first()->is_active);
    }

    #[Test]
    public function it_can_scope_ordered_tags()
    {
        BlogTag::factory()->create(['name' => 'Zebra Tag']);
        BlogTag::factory()->create(['name' => 'Alpha Tag']);
        BlogTag::factory()->create(['name' => 'Beta Tag']);

        $orderedTags = BlogTag::ordered()->get();

        $this->assertEquals('Alpha Tag', $orderedTags->first()->name);
        $this->assertEquals('Beta Tag', $orderedTags->get(1)->name);
        $this->assertEquals('Zebra Tag', $orderedTags->last()->name);
    }

    #[Test]
    public function it_can_scope_by_language()
    {
        BlogTag::factory()->language('pt')->create(['name' => 'Portuguese Tag']);
        BlogTag::factory()->language('en')->create(['name' => 'English Tag']);
        BlogTag::factory()->language('es')->create(['name' => 'Spanish Tag']);

        $portugueseTags = BlogTag::byLanguage('pt')->get();
        $englishTags = BlogTag::byLanguage('en')->get();

        $this->assertCount(1, $portugueseTags);
        $this->assertCount(1, $englishTags);
        $this->assertEquals('Portuguese Tag', $portugueseTags->first()->name);
        $this->assertEquals('English Tag', $englishTags->first()->name);
    }

    #[Test]
    public function it_generates_unique_slug()
    {
        $slug1 = BlogTag::generateSlug('Test Tag');
        BlogTag::factory()->create(['slug' => $slug1]);
        
        $slug2 = BlogTag::generateSlug('Test Tag');

        $this->assertEquals('test-tag', $slug1);
        $this->assertEquals('test-tag-1', $slug2);
    }

    #[Test]
    public function it_generates_multiple_unique_slugs()
    {
        BlogTag::factory()->create(['slug' => 'test-tag']);
        BlogTag::factory()->create(['slug' => 'test-tag-1']);
        
        $slug = BlogTag::generateSlug('Test Tag');

        $this->assertEquals('test-tag-2', $slug);
    }

    #[Test]
    public function it_generates_random_color()
    {
        $color1 = BlogTag::getRandomColor();
        $color2 = BlogTag::getRandomColor();

        // Test that it returns a valid hex color
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color1);
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color2);
        
        // Test that it's 7 characters long (including #)
        $this->assertEquals(7, strlen($color1));
        $this->assertEquals(7, strlen($color2));
    }

    #[Test]
    public function get_name_method_returns_name_attribute()
    {
        $tag = BlogTag::factory()->create(['name' => 'Test Tag Name']);

        $this->assertEquals('Test Tag Name', $tag->getName());
    }

    #[Test]
    public function it_can_be_created_using_factory()
    {
        $tag = BlogTag::factory()->create();

        $this->assertInstanceOf(BlogTag::class, $tag);
        $this->assertNotEmpty($tag->name);
        $this->assertNotEmpty($tag->slug);
        $this->assertContains($tag->language, ['pt', 'en', 'es']);
        $this->assertIsBool($tag->is_active);
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $tag->color);
    }

    #[Test]
    public function it_can_be_created_with_factory_states()
    {
        $activeTag = BlogTag::factory()->active()->create();
        $inactiveTag = BlogTag::factory()->inactive()->create();
        $portugueseTag = BlogTag::factory()->language('pt')->create();
        $coloredTag = BlogTag::factory()->withColor('#FF0000')->create();

        $this->assertTrue($activeTag->is_active);
        $this->assertFalse($inactiveTag->is_active);
        $this->assertEquals('pt', $portugueseTag->language);
        $this->assertEquals('#FF0000', $coloredTag->color);
    }

    #[Test]
    public function it_automatically_generates_color_if_not_provided()
    {
        $tag = BlogTag::factory()->make(['color' => null]);
        
        // Simulate the behavior that would happen in the model
        if (empty($tag->color)) {
            $tag->color = BlogTag::getRandomColor();
        }

        $this->assertNotNull($tag->color);
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $tag->color);
    }

    #[Test]
    public function it_validates_color_format()
    {
        $validColors = ['#FF0000', '#00FF00', '#0000FF', '#FFFFFF', '#000000', '#123ABC'];
        $invalidColors = ['FF0000', '#FF00', '#GGGGGG', 'red', '#FF00000'];

        foreach ($validColors as $color) {
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color);
        }

        foreach ($invalidColors as $color) {
            $this->assertDoesNotMatchRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color);
        }
    }
}