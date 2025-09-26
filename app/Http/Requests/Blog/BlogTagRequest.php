<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @method mixed route(string $key = null)
 * @method mixed input(string $key = null, mixed $default = null)
 * @method bool boolean(string $key, bool $default = false)
 * @method void merge(array $input)
 * @method mixed query(string $key = null, mixed $default = null)
 * @property string|null $name
 * @property string|null $description
 * @property string|null $language
 */
class BlogTagRequest extends FormRequest
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
        $tagId = $this->route('blog_tag') ? $this->route('blog_tag')->id : null;
        
        return [
            // Name - required
            'name' => 'required|string|max:255',
            
            // Description - optional
            'description' => 'nullable|string|max:1000',
            
            // Language - required
            'language' => 'required|string|in:pt,en,es',
            
            // Slug - unique
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_tags', 'slug')->ignore($tagId),
            ],
            
            // Color - hex color code
            'color' => [
                'required',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            ],
            
            // Active status
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            
            'language.required' => 'O idioma é obrigatório.',
            'language.in' => 'O idioma deve ser português (pt), inglês (en) ou espanhol (es).',
            
            'slug.required' => 'O slug é obrigatório.',
            'slug.regex' => 'O slug deve conter apenas letras minúsculas, números e hífens.',
            'slug.unique' => 'Este slug já está sendo usado por outra tag.',
            'slug.max' => 'O slug não pode ter mais de 255 caracteres.',
            
            'color.required' => 'A cor é obrigatória.',
            'color.regex' => 'A cor deve ser um código hexadecimal válido (ex: #FF0000 ou #F00).',
            
            'is_active.boolean' => 'O status ativo deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'language' => $this->input('language', 'pt'), // Default to Portuguese
        ]);

        // Generate slug if not provided
        if (empty($this->input('slug'))) {
            $name = $this->input('name');
            if ($name) {
                $this->merge([
                    'slug' => Str::slug($name),
                ]);
            }
        }

        // Generate random color if not provided
        if (empty($this->input('color'))) {
            $this->merge([
                'color' => $this->generateRandomColor(),
            ]);
        }

        // Ensure color starts with #
        if ($this->input('color') && !str_starts_with($this->input('color'), '#')) {
            $this->merge([
                'color' => '#' . $this->input('color'),
            ]);
        }
    }

    /**
     * Get the validated data with proper type casting.
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        
        // Ensure boolean values are properly cast
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        
        return $validated;
    }

    /**
     * Generate a random hex color.
     */
    private function generateRandomColor(): string
    {
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
            '#F8C471', '#82E0AA', '#F1948A', '#85C1E9', '#D7BDE2',
            '#A3E4D7', '#F9E79F', '#D5A6BD', '#AED6F1', '#A9DFBF',
        ];
        
        return $colors[array_rand($colors)];
    }
}