<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ExploreSliderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => [
                'title'             => 'nullable|string|max:255',
                'description'       => 'nullable|string',
                'link'              => 'nullable|url|max:255',
                'image'             => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            'PUT', 'PATCH' => [
                'title'             => 'nullable|string|max:255',
                'description'       => 'nullable|string',
                'link'              => 'nullable|url|max:255',
                'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            default => []
        };
    }
}