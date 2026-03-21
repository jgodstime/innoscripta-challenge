<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetArticleRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'nullable|integer',
            'source_id' => 'nullable|integer',
            'published_from' => 'nullable|date',
            'published_to' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:50',
            'preference_by_author' => 'nullable|boolean',
            'preference_by_source' => 'nullable|boolean',
            'preference_by_category' => 'nullable|boolean',
        ];
    }
}
