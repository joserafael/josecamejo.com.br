<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blog Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the blog system including
    | image upload settings, validation rules, and storage paths.
    |
    */

    'images' => [
        /*
        |--------------------------------------------------------------------------
        | Image Storage Settings
        |--------------------------------------------------------------------------
        */
        'disk' => 'public',
        'path' => 'blog/images',
        'url_prefix' => '/storage/blog/images',

        /*
        |--------------------------------------------------------------------------
        | Image Validation Rules
        |--------------------------------------------------------------------------
        */
        'validation' => [
            'max_size' => 5120, // 5MB in kilobytes
            'min_width' => 100,
            'max_width' => 2048,
            'min_height' => 100,
            'max_height' => 2048,
            'allowed_mimes' => ['jpeg', 'jpg', 'png', 'webp', 'gif'],
            'allowed_extensions' => ['jpeg', 'jpg', 'png', 'webp', 'gif'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Image Processing Settings
        |--------------------------------------------------------------------------
        */
        'processing' => [
            'auto_optimize' => true,
            'quality' => 85,
            'create_thumbnails' => true,
            'thumbnail_sizes' => [
                'small' => ['width' => 150, 'height' => 150],
                'medium' => ['width' => 300, 'height' => 300],
                'large' => ['width' => 600, 'height' => 600],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Image Naming Convention
        |--------------------------------------------------------------------------
        */
        'naming' => [
            'use_uuid' => true,
            'preserve_original_name' => true,
            'slug_separator' => '-',
        ],
    ],

    'videos' => [
        /*
        |--------------------------------------------------------------------------
        | Video Storage Settings
        |--------------------------------------------------------------------------
        */
        'disk' => 'public',
        'path' => 'blog/videos',
        'thumbnail_path' => 'blog/videos/thumbnails',
        'url_prefix' => '/storage/blog/videos',

        /*
        |--------------------------------------------------------------------------
        | Video Validation Rules
        |--------------------------------------------------------------------------
        */
        'validation' => [
            'max_size' => 102400, // 100MB in kilobytes
            'min_duration' => 1, // 1 second
            'max_duration' => 3600, // 1 hour in seconds
            'allowed_mimes' => ['mp4', 'webm', 'avi', 'mov', 'wmv', 'flv'],
            'allowed_extensions' => ['mp4', 'webm', 'avi', 'mov', 'wmv', 'flv'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Video Thumbnail Settings
        |--------------------------------------------------------------------------
        */
        'thumbnail' => [
            'max_size' => 5120, // 5MB in kilobytes
            'allowed_mimes' => ['jpeg', 'jpg', 'png', 'webp'],
            'allowed_extensions' => ['jpeg', 'jpg', 'png', 'webp'],
            'auto_generate' => false, // Set to true if you want to auto-generate thumbnails from video
            'quality' => 85,
            'max_width' => 1920,
            'max_height' => 1080,
        ],

        /*
        |--------------------------------------------------------------------------
        | Video Processing Settings
        |--------------------------------------------------------------------------
        */
        'processing' => [
            'auto_optimize' => false, // Video optimization can be resource intensive
            'extract_metadata' => true,
            'generate_preview' => false, // Generate preview clips
            'preview_duration' => 10, // seconds
        ],

        /*
        |--------------------------------------------------------------------------
        | Video Naming Convention
        |--------------------------------------------------------------------------
        */
        'naming' => [
            'use_uuid' => true,
            'preserve_original_name' => true,
            'slug_separator' => '-',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | General Blog Settings
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],

    'languages' => [
        'supported' => ['en', 'es', 'pt'],
        'default' => 'en',
    ],

    'status' => [
        'default' => true,
        'options' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],

];