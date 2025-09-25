<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettingRequest extends FormRequest
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
        $rules = [
            'app_name' => 'required|string|max:255',
            'app_description' => 'required|string|max:255',
            'app_keywords' => 'required|string|max:255',
            'app_email' => 'required|string|max:255',
            'app_robots' => 'required|string|max:255',
            'app_url' => 'required|string|max:255',
            'app_timezone' => 'required|string|max:255',
            'app_currency' => 'required|string|max:255',
            'warning_comments_message' => 'required|string|max:255',
            'app_logo' => 'required',
            'app_favicon' => 'required',
            'app_social_media' => 'required|json',
            'app_whatsapp' => 'required|string|max:255',
        ];

        if($this->hasFile('app_logo')) {
           $rules['app_logo'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120';
        } else {
            $rules['app_logo'] = 'nullable|string';
        }

        if($this->hasFile('app_favicon')) {
            $rules['app_favicon'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120';
        } else {
            $rules['app_favicon'] = 'nullable|string';
        }

        return $rules;
    }
}
