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