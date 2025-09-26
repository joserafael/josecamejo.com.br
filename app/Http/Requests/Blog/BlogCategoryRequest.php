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
class BlogCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ? $this->route('category')->id : null;
        
        return [
            // Name - required
            'name' => 'required|string|max:255',
            
            // Description - optional
            'description' => 'nullable|string|max:1000',
            
            // Language - required
            'language' => 'required|string|in:pt,en,es',
            
            // Slug - unique except for current record
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_categories', 'slug')->ignore($categoryId),
            ],
            
            // Sort order
            'sort_order' => 'nullable|integer|min:0|max:999',
            
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
            'slug.unique' => 'Este slug já está sendo usado por outra categoria.',
            'slug.max' => 'O slug não pode ter mais de 255 caracteres.',
            
            'sort_order.integer' => 'A ordem de classificação deve ser um número inteiro.',
            'sort_order.min' => 'A ordem de classificação deve ser no mínimo 0.',
            'sort_order.max' => 'A ordem de classificação deve ser no máximo 999.',
            
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
            'sort_order' => $this->input('sort_order', 0),
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
    }

    /**
     * Get the validated data with proper type casting.
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        
        // Ensure boolean values are properly cast
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        
        return $validated;
    }
}