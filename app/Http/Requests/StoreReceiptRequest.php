<?php

namespace App\Http\Requests;

use App\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReceiptRequest extends FormRequest
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
            'type' => [
                'required',
                Rule::enum(PaymentType::class),
            ],
            'amount' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $type = $this->input('type');

                    // Validate amount based on payment type
                    switch ($type) {
                        case PaymentType::BUYER_FEE->value:
                            if ($value !== 3000000) {
                                $fail('مبلغ کارمزد خریدار باید دقیقاً 3,000,000 تومان باشد.');
                            }
                            break;
                        case PaymentType::SELLER_FEE->value:
                            if ($value !== 3000000) {
                                $fail('مبلغ کارمزد فروشنده باید دقیقاً 3,000,000 تومان باشد.');
                            }
                            break;
                        case PaymentType::BUYER_PURCHASE_AMOUNT->value:
                            // Amount should match the accepted bid amount
                            // This will be validated in the controller
                            break;
                    }
                },
            ],
            'receipt_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5MB in KB
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
            'type.required' => 'نوع پرداخت الزامی است.',
            'type.enum' => 'نوع پرداخت نامعتبر است.',
            'amount.required' => 'مبلغ پرداخت الزامی است.',
            'amount.integer' => 'مبلغ پرداخت باید یک عدد صحیح باشد.',
            'amount.min' => 'مبلغ پرداخت باید حداقل 1 تومان باشد.',
            'receipt_image.required' => 'تصویر رسید پرداخت الزامی است.',
            'receipt_image.image' => 'فایل باید یک تصویر معتبر باشد.',
            'receipt_image.mimes' => 'فرمت تصویر باید jpg، jpeg، png یا webp باشد.',
            'receipt_image.max' => 'حجم تصویر نباید بیشتر از 5 مگابایت باشد.',
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
            'type' => 'نوع پرداخت',
            'amount' => 'مبلغ پرداخت',
            'receipt_image' => 'تصویر رسید پرداخت',
        ];
    }
}

