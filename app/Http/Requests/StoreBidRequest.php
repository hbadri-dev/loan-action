<?php

namespace App\Http\Requests;

use App\Models\Auction;
use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean the amount field by removing commas and converting to integer
        if ($this->has('amount')) {
            $amount = $this->input('amount');
            \Log::info('Raw amount received:', ['amount' => $amount, 'type' => gettype($amount)]);

            if (is_string($amount)) {
                $cleanAmount = str_replace(',', '', $amount);
                $intAmount = (int) $cleanAmount;
                \Log::info('Cleaned amount:', ['original' => $amount, 'cleaned' => $cleanAmount, 'int' => $intAmount]);

                $this->merge([
                    'amount' => $intAmount
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $auction = $this->route('auction');

                    if (!$auction instanceof Auction) {
                        $fail('مزایده یافت نشد.');
                        return;
                    }

                    // Check if amount is greater than minimum purchase price
                    if ($value <= $auction->min_purchase_price) {
                        $fail('مبلغ پیشنهادی باید بیشتر از حداقل قیمت خرید (' . number_format($auction->min_purchase_price) . ' تومان) باشد.');
                        return;
                    }

                    // Check if amount is greater than current highest bid
                    $highestBid = $auction->bids()
                        ->where('status', \App\Enums\BidStatus::HIGHEST)
                        ->first();

                    // Allow users to update their own bid even if it's the highest
                    $currentUser = auth()->user();
                    if ($highestBid && $value <= $highestBid->amount) {
                        // If the highest bid belongs to the current user, allow them to update it
                        if ($highestBid->buyer_id !== $currentUser->id) {
                            $fail('مبلغ پیشنهادی باید بیشتر از بالاترین پیشنهاد فعلی (' . number_format($highestBid->amount) . ' تومان) باشد.');
                            return;
                        }
                    }
                },
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
            'amount.required' => 'مبلغ پیشنهادی الزامی است.',
            'amount.integer' => 'مبلغ پیشنهادی باید یک عدد صحیح باشد.',
            'amount.min' => 'مبلغ پیشنهادی باید حداقل 1 تومان باشد.',
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
            'amount' => 'مبلغ پیشنهادی',
        ];
    }
}
