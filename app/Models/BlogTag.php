<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'language',
        'is_active',
        'color'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Scope for active tags
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive tags
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for tags in a specific language
     */
    public function scopeInLanguage($query, $language = null)
    {
        $language = $language ?: app()->getLocale();
        return $query->where('language', $language);
    }

    /**
     * Get posts associated with this tag
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag');
    }

    /**
     * Get published posts associated with this tag
     */
    public function publishedPosts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag')
                    ->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope for tags by language (alias for inLanguage)
     */
    public function scopeByLanguage($query, $language = null)
    {
        return $this->scopeInLanguage($query, $language);
    }

    /**
     * Scope for ordered tags
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
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
     * Get a random color for the tag
     */
    public static function getRandomColor(): string
    {
        $colors = [
            '#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107',
            '#17a2b8', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997'
        ];
        
        return $colors[array_rand($colors)];
    }

    /**
     * Get the name of the tag
     */
    public function getName(): string
    {
        return $this->name;
    }
}
