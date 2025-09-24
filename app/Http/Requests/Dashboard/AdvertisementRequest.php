<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class AdvertisementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'required',
                'string'
            ],
            'price' => [
                'nullable',
                'numeric'
            ],
            'category' => [
                'required',
                'array'
            ],
            'category.id' => [
                'required',
                'exists:categories,id'
            ],
            'sub_category' => [
                'nullable',
                'array'
            ],
            'sub_category.id' => [
                'nullable',
                'exists:sub_categories,id'
            ],
            'sub_sub_category' => [
                'nullable',
                'array'
            ],
            'sub_sub_category.id' => [
                'nullable',
                'exists:sub_sub_categories,id'
            ],
            'latitude' => [
                'required',
                'numeric'
            ],
            'longitude' => [
                'required',
                'numeric'
            ],
            'country' => [
                'required',
                'array'
            ],
            'country.id' => [
                'required',
                'exists:countries,id'
            ],
            'city' => [
                'nullable',
                'array'
            ],
            'city.id' => [
                'nullable',
                'exists:cities,id'
            ],
            'user' => [
                'required',
                'array'
            ],
            'user.country_code' => [
                'required',
                'string'
            ],
            'user.phone_number' => [
                'required',
                'string'
            ],
            'filters' => [
                'nullable',
                'array'
            ],
            'filters.*.filter_id' => [
                'required',
                'exists:filters,id'
            ],
            'filters.*.value' => [
                'required',
                'string'
            ],
            'media' => [
                'required',
                'array'
            ],
            'media.*.id' => [
                'required',
                'exists:files,id'
            ],
            'media.*.is_main' => [
                'required',
                'boolean'
            ]
        ];
    }
}
