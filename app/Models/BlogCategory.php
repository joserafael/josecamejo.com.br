<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'language',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get subcategories for this category
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(BlogSubcategory::class);
    }

    /**
     * Get active subcategories for this category
     */
    public function activeSubcategories(): HasMany
    {
        return $this->hasMany(BlogSubcategory::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get subcategories for this category in a specific language
     */
    public function subcategoriesInLanguage($language = null): HasMany
    {
        $language = $language ?: app()->getLocale();
        return $this->hasMany(BlogSubcategory::class)->where('language', $language);
    }

    /**
     * Get posts for this category
     */
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Get published posts for this category
     */
    public function publishedPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class)->where('status', 'published')->where('published_at', '<=', now());
    }

    /**
     * Get active subcategories for this category in a specific language
     */
    public function activeSubcategoriesInLanguage($language = null): HasMany
    {
        $language = $language ?: app()->getLocale();
        return $this->hasMany(BlogSubcategory::class)
            ->where('is_active', true)
            ->where('language', $language)
            ->orderBy('sort_order');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive categories
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for categories in a specific language
     */
    public function scopeInLanguage($query, $language = null)
    {
        $language = $language ?: app()->getLocale();
        return $query->where('language', $language);
    }

    /**
     * Scope for categories by language (alias for inLanguage)
     */
    public function scopeByLanguage($query, $language = null)
    {
        return $this->scopeInLanguage($query, $language);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Generate slug from name
     */
    public static function generateSlug($name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the name of the category
     */
    public function getName(): string
    {
        return $this->name;
    }
}
