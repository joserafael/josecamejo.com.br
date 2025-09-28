<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BlogVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $config = config('blog.videos.validation');
        
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'language' => ['required', 'string', Rule::in(config('blog.languages.supported'))],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:999'],
            'is_active' => ['boolean'],
            'video' => [
                'nullable',
                'file',
                'mimes:' . implode(',', $config['allowed_mimes']),
                'max:' . $config['max_size'],
            ],
            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2MB for thumbnails
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $config = config('blog.videos.validation');
        
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'language.required' => 'The language field is required.',
            'language.in' => 'The selected language is invalid.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.min' => 'The sort order must be at least 1.',
            'sort_order.max' => 'The sort order may not be greater than 999.',
            'video.required' => 'A video file is required.',
            'video.file' => 'The file must be a valid video file.',
            'video.mimes' => 'The video must be a file of type: ' . implode(', ', $config['allowed_mimes']) . '.',
            'video.max' => 'The video may not be greater than ' . ($config['max_size'] / 1024) . 'MB.',
            'thumbnail.image' => 'The thumbnail must be an image.',
            'thumbnail.mimes' => 'The thumbnail must be a file of type: jpeg, jpg, png, webp.',
            'thumbnail.max' => 'The thumbnail may not be greater than 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
            'language' => 'language',
            'sort_order' => 'sort order',
            'is_active' => 'active status',
            'video' => 'video',
            'thumbnail' => 'thumbnail',
        ];
    }
}
