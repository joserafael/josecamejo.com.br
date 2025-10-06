<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BlogPost;
use App\Models\BlogComment;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\CaptchaService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @method mixed route(string $key = null)
 */
class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => [
                'nullable',
                'exists:blog_comments,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $parentComment = BlogComment::find($value);
                        if (!$parentComment) {
                            $fail('Invalid parent comment.');
                        } elseif ($parentComment->status !== 'approved') {
                            $fail('Cannot reply to an unapproved comment.');
                        }
                    }
                }
            ],
            'author_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/', // Only letters, spaces, hyphens, apostrophes, and dots
                function ($attribute, $value, $fail) {
                    // Check for spam patterns
                    $spamPatterns = ['viagra', 'casino', 'poker', 'loan', 'mortgage', 'pharmacy'];
                    foreach ($spamPatterns as $pattern) {
                        if (stripos($value, $pattern) !== false) {
                            $fail('Invalid name detected.');
                        }
                    }
                }
            ],
            'author_email' => [
                'required',
                'email:rfc',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check for disposable email domains
                    $disposableDomains = ['10minutemail.com', 'tempmail.org', 'guerrillamail.com'];
                    $domain = substr(strrchr($value, "@"), 1);
                    if (in_array($domain, $disposableDomains)) {
                        $fail('Disposable email addresses are not allowed.');
                    }
                }
            ],
            'author_website' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/.*)?$/'
            ],
            'content' => [
                'required',
                'string',
                'min:10',
                'max:2000',
                function ($attribute, $value, $fail) {
                    // Check for spam content
                    $spamPatterns = [
                        'http://', 'https://', 'www.', '.com', '.net', '.org',
                        'click here', 'buy now', 'free money', 'make money',
                        'viagra', 'casino', 'poker', 'loan', 'mortgage'
                    ];
                    
                    $spamCount = 0;
                    foreach ($spamPatterns as $pattern) {
                        if (stripos($value, $pattern) !== false) {
                            $spamCount++;
                        }
                    }
                    
                    if ($spamCount >= 3) {
                        $fail('Comment appears to be spam.');
                    }
                    
                    // Check for excessive capitalization
                    $upperCount = preg_match_all('/[A-Z]/', $value);
                    $totalChars = strlen(preg_replace('/[^a-zA-Z]/', '', $value));
                    if ($totalChars > 0 && ($upperCount / $totalChars) > 0.5) {
                        $fail('Please avoid excessive use of capital letters.');
                    }
                    
                    // Check for repeated characters
                    if (preg_match('/(.)\1{4,}/', $value)) {
                        $fail('Please avoid excessive repetition of characters.');
                    }
                }
            ],
            'captcha_answer' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!CaptchaService::validate($value)) {
                        $fail('A resposta da verificação matemática está incorreta.');
                    }
                }
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'author_name.regex' => 'Name can only contain letters, spaces, hyphens, apostrophes, and dots.',
            'author_email.email' => 'Please provide a valid email address.',
            'author_website.regex' => 'Please provide a valid website URL.',
            'content.min' => 'Comment must be at least 10 characters long.',
            'content.max' => 'Comment cannot exceed 2000 characters.',
            'captcha_answer.required' => 'Por favor, resolva a verificação matemática.',
            'captcha_answer.integer' => 'A resposta deve ser um número inteiro.',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        // Apply rate limiting
        $key = 'comment-submission:' . request()->ip();
        RateLimiter::hit($key, 300); // 5 minutes

        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You are not authorized to submit comments at this time.'
        );
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
