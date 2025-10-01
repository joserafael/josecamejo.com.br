<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogSubcategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'blog_category_id',
        'name',
        'slug',
        'description',
        'language',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'blog_category_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the category that owns this subcategory
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Get posts for this subcategory
     */
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Get published posts for this subcategory
     */
    public function publishedPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class)->where('status', 'published')->where('published_at', '<=', now());
    }

    /**
     * Scope for active subcategories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive subcategories
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for subcategories in a specific language
     */
    public function scopeInLanguage($query, $language = null)
    {
        $language = $language ?: app()->getLocale();
        return $query->where('language', $language);
    }

    /**
     * Scope for subcategories by language (alias for inLanguage)
     */
    public function scopeByLanguage($query, $language = null)
    {
        return $this->scopeInLanguage($query, $language);
    }

    /**
     * Scope for ordered subcategories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope for subcategories by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('blog_category_id', $categoryId);
    }

    /**
     * Scope for subcategories by category and language
     */
    public function scopeByCategoryAndLanguage($query, $categoryId, $language = null)
    {
        $language = $language ?: app()->getLocale();
        return $query->where('blog_category_id', $categoryId)->where('language', $language);
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
     * Get the name of the subcategory
     */
    public function getName(): string
    {
        return $this->name;
    }
}
