<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SplashScreenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => [
                'title'     => 'required|string',
                'content'   => 'required|string',
                'image'     => 'required|image|max:2048',
            ],
            'PUT' => [
                'title'     => 'required|string',
                'content'   => 'required|string',
                'image'     => 'nullable|image|max:2048',
            ],
            default => []
        };
    }
}
