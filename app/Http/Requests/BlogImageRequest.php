<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BlogImageRequest extends FormRequest
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
        $config = config('blog.images.validation');
        
        return [
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'language' => ['required', 'string', Rule::in(config('blog.languages.supported'))],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:999'],
            'is_active' => ['boolean'],
            'image' => [
                'nullable',
                'image',
                'mimes:' . implode(',', $config['allowed_mimes']),
                'max:' . $config['max_size'],
                'dimensions:min_width=' . $config['min_width'] . 
                          ',max_width=' . $config['max_width'] . 
                          ',min_height=' . $config['min_height'] . 
                          ',max_height=' . $config['max_height'],
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
        $config = config('blog.images.validation');
        
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'alt_text.required' => 'The alt text field is required.',
            'alt_text.max' => 'The alt text may not be greater than 255 characters.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'language.required' => 'The language field is required.',
            'language.in' => 'The selected language is invalid.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.min' => 'The sort order must be at least 1.',
            'sort_order.max' => 'The sort order may not be greater than 999.',
            'image.required' => 'An image file is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: ' . implode(', ', $config['allowed_mimes']) . '.',
            'image.max' => 'The image may not be greater than ' . ($config['max_size'] / 1024) . 'MB.',
            'image.dimensions' => 'The image dimensions must be between ' . 
                                $config['min_width'] . 'x' . $config['min_height'] . 
                                ' and ' . $config['max_width'] . 'x' . $config['max_height'] . ' pixels.',
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
            'alt_text' => 'alt text',
            'description' => 'description',
            'language' => 'language',
            'sort_order' => 'sort order',
            'is_active' => 'active status',
            'image' => 'image',
        ];
    }
}
