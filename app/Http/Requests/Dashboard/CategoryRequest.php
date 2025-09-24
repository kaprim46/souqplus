<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'ok'        => false,
            'message'   => __('Validation error'),
            'errors'    => $validator->errors()
        ])->setStatusCode(422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST', 'PUT' => [
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'meta_title' => 'required|string|max:255',
                'meta_description' => 'required|string|max:255',
                'meta_keywords' => 'required|string|max:255',
                'meta_robots' => 'required|string|max:255',
                'filters'     => 'nullable|array',
                'filters.*'   => 'integer|exists:filters,id'
            ],
            default => [
                'search' => 'nullable|string|max:255',
                'pagination' => 'nullable|integer',

            ],
        };
    }
}
