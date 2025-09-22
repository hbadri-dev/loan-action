<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OTPRequest extends FormRequest
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
            'phone' => [
                'required',
                'string',
                'regex:/^09[0-9]{9}$/',
            ],
            'purpose' => [
                'required',
                'string',
                Rule::in(['login-otp', 'contract-confirmation']),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'شماره تلفن الزامی است.',
            'phone.string' => 'شماره تلفن باید متن باشد.',
            'phone.regex' => 'شماره تلفن باید با 09 شروع شده و 11 رقم باشد.',
            'purpose.required' => 'هدف ارسال کد الزامی است.',
            'purpose.string' => 'هدف ارسال کد باید متن باشد.',
            'purpose.in' => 'هدف ارسال کد نامعتبر است.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'phone' => 'شماره تلفن',
            'purpose' => 'هدف ارسال کد',
        ];
    }
}

