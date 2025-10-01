<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'language',
        'status',
        'is_featured',
        'allow_comments',
        'views_count',
        'sort_order',
        'published_at',
        'blog_category_id',
        'blog_subcategory_id',
        'user_id'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'views_count' => 'integer',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blogPost) {
            // Generate slug if not provided
            if (empty($blogPost->slug)) {
                $blogPost->slug = static::generateSlug($blogPost->title);
            }
            
            // Set published_at when status changes to published
            if ($blogPost->status === 'published' && !$blogPost->published_at) {
                $blogPost->published_at = now();
            }
        });

        static::updating(function ($blogPost) {
            // Update slug if title changed
            if ($blogPost->isDirty('title') && empty($blogPost->slug)) {
                $blogPost->slug = static::generateSlug($blogPost->title);
            }
            
            // Set published_at when status changes to published
            if ($blogPost->isDirty('status') && $blogPost->status === 'published' && !$blogPost->published_at) {
                $blogPost->published_at = now();
            }
        });
    }

    /**
     * Generate a unique slug
     */
    public static function generateSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->when($id, function ($query, $id) {
            return $query->where('id', '!=', $id);
        })->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the category that owns this post
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Get the subcategory that owns this post
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(BlogSubcategory::class, 'blog_subcategory_id');
    }

    /**
     * Get the author of this post
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the tags associated with this post
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    /**
     * Get the images associated with this post
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(BlogImage::class, 'blog_post_image')
                    ->withPivot(['sort_order', 'caption'])
                    ->orderBy('pivot_sort_order');
    }

    /**
     * Get the videos associated with this post
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(BlogVideo::class, 'blog_post_video')
                    ->withPivot(['sort_order', 'caption'])
                    ->orderBy('pivot_sort_order');
    }

    /**
     * Get the comments for this post
     */
    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Get approved comments for this post
     */
    public function approvedComments()
    {
        return $this->hasMany(BlogComment::class)->approved();
    }

    /**
     * Get top-level approved comments (not replies)
     */
    public function topLevelComments()
    {
        return $this->hasMany(BlogComment::class)->approved()->topLevel()->with('replies');
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope for draft posts
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for posts in a specific language
     */
    public function scopeInLanguage($query, $language = null)
    {
        $language = $language ?: app()->getLocale();
        return $query->where('language', $language);
    }

    /**
     * Scope for posts by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('blog_category_id', $categoryId);
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the excerpt or truncated content
     */
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return Str::limit(strip_tags($this->content), 150);
    }

    /**
     * Get the reading time estimate
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute
        
        return $readingTime . ' min read';
    }

    /**
     * Check if the post is published
     */
    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at && $this->published_at <= now();
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
