<?php

namespace App\View\Components;

use App\Models\SellerSale;
use Illuminate\View\Component;

class SellerStepProgress extends Component
{
    public SellerSale $sellerSale;
    public array $steps;

    /**
     * Create a new component instance.
     */
    public function __construct(SellerSale $sellerSale)
    {
        // Refresh the model to get the latest data
        $sellerSale->refresh();
        $this->sellerSale = $sellerSale;
        $this->steps = $this->getSteps();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.seller-step-progress');
    }

    /**
     * Get all seller steps
     */
    public function getSteps(): array
    {
        $allSteps = [
            [
                'number' => 1,
                'title' => 'اطلاعات مزایده',
                'description' => 'بررسی جزئیات مزایده و شرایط فروش'
            ],
            [
                'number' => 2,
                'title' => 'تأیید قرارداد',
                'description' => 'تأیید متن قرارداد و ارسال کد تأیید'
            ],
            [
                'number' => 3,
                'title' => 'پرداخت کارمزد',
                'description' => 'آپلود رسید پرداخت کارمزد فروشنده'
            ],
            [
                'number' => 4,
                'title' => 'پذیرش پیشنهاد',
                'description' => 'بررسی و پذیرش پیشنهادات خریداران'
            ],
            [
                'number' => 5,
                'title' => 'انتظار پرداخت خریدار',
                'description' => 'منتظر تأیید پرداخت مبلغ خرید توسط خریدار'
            ],
            [
                'number' => 6,
                'title' => 'انتقال وام',
                'description' => 'انتقال وام به خریدار و آپلود رسید'
            ],
            [
                'number' => 7,
                'title' => 'تأیید انتقال',
                'description' => 'انتظار تأیید انتقال توسط خریدار'
            ],
            [
                'number' => 8,
                'title' => 'تکمیل فروش',
                'description' => 'فرآیند فروش با موفقیت تکمیل شد'
            ]
        ];

        $currentStepNumber = (int) $this->sellerSale->current_step;

        // Update step statuses
        foreach ($allSteps as &$step) {
            $stepNumber = (int) $step['number'];

            if ($stepNumber < $currentStepNumber) {
                $step['status'] = 'completed';
                $step['icon'] = 'check';
            } elseif ($stepNumber === $currentStepNumber) {
                $step['status'] = 'current';
                $step['icon'] = 'current';
            } else {
                $step['status'] = 'pending';
                $step['icon'] = 'pending';
            }

        }

        return $allSteps;
    }

    /**
     * Get step title by step number
     */
    public function getStepTitle(int $stepNumber): string
    {
        $steps = [
            1 => 'اطلاعات مزایده',
            2 => 'تأیید قرارداد',
            3 => 'پرداخت کارمزد',
            4 => 'پذیرش پیشنهاد',
            5 => 'انتظار پرداخت خریدار',
            6 => 'انتقال وام',
            7 => 'تأیید انتقال',
            8 => 'تکمیل فروش'
        ];

        return $steps[$stepNumber] ?? 'مرحله ناشناخته';
    }
}
