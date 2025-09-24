<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class CustomerRequest extends FormRequest
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
            'name'          => 'required|string|max:255',
            'country_code' => ['required', 'string', 'max:255'],
            'is_verified'   => 'boolean',
        ];

        /**
         * If the request is PUT and the current_password or password is set, then we need to validate the current_password
         */
        if($this->method() === 'PUT' && ($this->get('current_password') || $this->get('password'))) {
            $rules['current_password'] = [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (Hash::check($value, DB::table('users')->where('id', $this->get('id'))->value('password'))) {
                        return true;
                    }

                    $fail('The current password is incorrect.');

                    return false;
                },
            ];
            $rules['password']         = 'required|string|min:8|same:confirm_password';
        }


        return match ($this->method()) {
            'POST' => array_merge($rules, [
                'phone_number' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:users,phone_number',
                    function ($attribute, $value, $fail) {
                        $phoneNumberUtil = PhoneNumberUtil::getInstance();
                        try {
                            $phoneNumber = $phoneNumberUtil->parse("{$this->get('country_code')}{$value}", null);

                            if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                                $fail('Invalid phone number');
                            }
                        } catch (NumberParseException $e) {
                            $fail('Invalid phone number: ' . $e->getMessage());
                        }
                    },
                ],
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|string|min:8|same:confirm_password',
            ]),
            'PUT' => array_merge($rules, [
                'phone_number' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:users,phone_number,' . $this->get('id'),
                    function ($attribute, $value, $fail) {
                        $phoneNumberUtil = PhoneNumberUtil::getInstance();
                        try {
                            $phoneNumber = $phoneNumberUtil->parse("{$this->get('country_code')}{$value}", null);

                            if (!$phoneNumberUtil->isValidNumber($phoneNumber)) {
                                $fail('Invalid phone number');
                            }
                        } catch (NumberParseException $e) {
                            $fail('Invalid phone number: ' . $e->getMessage());
                        }
                    },
                ],
                'email'         => 'required|email|unique:users,email,' . $this->get('id'),
            ]),
            default => [
                'search' => 'nullable|string|max:255',
            ],
        };
    }
}
