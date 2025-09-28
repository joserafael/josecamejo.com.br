<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'alt_text',
        'filename',
        'original_filename',
        'path',
        'mime_type',
        'file_size',
        'width',
        'height',
        'language',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blogImage) {
            // Generate slug if not provided
            if (empty($blogImage->slug)) {
                $blogImage->slug = static::generateSlug($blogImage->title);
            }

            // Set alt_text to title if not provided
            if (empty($blogImage->alt_text)) {
                $blogImage->alt_text = $blogImage->title;
            }
        });

        static::updating(function ($blogImage) {
            // Update slug if title changed
            if ($blogImage->isDirty('title') && empty($blogImage->slug)) {
                $blogImage->slug = static::generateSlug($blogImage->title);
            }

            // Set alt_text to title if not provided
            if (empty($blogImage->alt_text)) {
                $blogImage->alt_text = $blogImage->title;
            }
        });
    }

    /**
     * Scope for active images
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive images
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for images in a specific language
     */
    public function scopeInLanguage($query, $language = null)
    {
        $language = $language ?: app()->getLocale();
        return $query->where('language', $language);
    }

    /**
     * Scope for images by language (alias for inLanguage)
     */
    public function scopeByLanguage($query, $language = null)
    {
        return $this->scopeInLanguage($query, $language);
    }

    /**
     * Scope for ordered images
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * Generate a unique slug for the image
     */
    public static function generateSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Get the full URL for the image (method version)
     */
    public function getUrl()
    {
        return config('blog.images.url_prefix') . '/' . $this->path;
    }

    /**
     * Scope for filtering by language
     */
    public function scopeLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get image dimensions as string
     */
    public function getDimensionsAttribute()
    {
        if ($this->width && $this->height) {
            return $this->width . ' × ' . $this->height;
        }
        return null;
    }

    /**
     * Check if image is an image file
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get the name for display
     */
    public function getName()
    {
        return $this->title;
    }
}
