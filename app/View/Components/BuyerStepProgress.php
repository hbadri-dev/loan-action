<?php

namespace App\View\Components;

use App\Models\BuyerProgress;
use Illuminate\View\Component;

class BuyerStepProgress extends Component
{
    public BuyerProgress $progress;
    public array $steps;

    /**
     * Create a new component instance.
     */
    public function __construct(BuyerProgress $progress)
    {
        $this->progress = $progress;
        $this->steps = $this->getSteps();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.buyer-step-progress');
    }

    /**
     * Get all steps with their status
     */
    private function getSteps(): array
    {
        $allSteps = [
            [
                'number' => 1,
                'name' => 'details',
                'title' => 'جزئیات وام',
                'description' => 'بررسی و تأیید جزئیات وام',
                'route' => route('buyer.auction.details', $this->progress->auction),
            ],
            [
                'number' => 2,
                'name' => 'contract',
                'title' => 'تأیید قرارداد',
                'description' => 'مطالعه و تأیید متن قرارداد',
                'route' => route('buyer.auction.contract', $this->progress->auction),
            ],
            [
                'number' => 3,
                'name' => 'payment',
                'title' => 'پرداخت کارمزد',
                'description' => 'پرداخت کارمزد شرکت در مزایده',
                'route' => route('buyer.auction.payment', $this->progress->auction),
            ],
            [
                'number' => 4,
                'name' => 'bid',
                'title' => 'ثبت پیشنهاد',
                'description' => 'ثبت پیشنهاد قیمت خرید',
                'route' => route('buyer.auction.bid', $this->progress->auction),
            ],
            [
                'number' => 5,
                'name' => 'waiting-seller',
                'title' => 'انتظار تأیید فروشنده',
                'description' => 'انتظار تأیید پیشنهاد توسط فروشنده',
                'route' => route('buyer.auction.waiting-seller', $this->progress->auction),
            ],
            [
                'number' => 6,
                'name' => 'purchase-payment',
                'title' => 'پرداخت مبلغ خرید',
                'description' => 'پرداخت مبلغ کامل خرید',
                'route' => route('buyer.auction.purchase-payment', $this->progress->auction),
            ],
            [
                'number' => 7,
                'name' => 'awaiting-seller-transfer',
                'title' => 'انتظار انتقال فروشنده',
                'description' => 'انتظار انتقال وام توسط فروشنده',
                'route' => route('buyer.auction.awaiting-seller-transfer', $this->progress->auction),
            ],
            [
                'number' => 8,
                'name' => 'confirm-transfer',
                'title' => 'تأیید انتقال وام',
                'description' => 'تأیید دریافت وام',
                'route' => route('buyer.auction.confirm-transfer', $this->progress->auction),
            ],
            [
                'number' => 9,
                'name' => 'complete',
                'title' => 'تکمیل شده',
                'description' => 'فرآیند خرید وام تکمیل شد',
                'route' => route('buyer.auction.complete', $this->progress->auction),
            ],
        ];

        $currentStepNumber = $this->getCurrentStepNumber();

        foreach ($allSteps as &$step) {
            if ($step['number'] < $currentStepNumber) {
                $step['status'] = 'completed';
                $step['icon'] = 'check';
            } elseif ($step['number'] === $currentStepNumber) {
                // Special case: if we're on step 1 (details), show it as completed
                // since the user has already clicked the participation button
                if ($step['number'] === 1 && $this->progress->step_name === 'details') {
                    $step['status'] = 'completed';
                    $step['icon'] = 'check';
                } else {
                    $step['status'] = 'current';
                    $step['icon'] = 'current';
                }
            } else {
                $step['status'] = 'pending';
                $step['icon'] = 'pending';
            }
        }

        return $allSteps;
    }

    /**
     * Get current step number based on progress
     */
    private function getCurrentStepNumber(): int
    {
        $stepOrder = [
            'details' => 1,
            'contract' => 2,
            'payment' => 3,
            'bid' => 4,
            'waiting-seller' => 5,
            'purchase-payment' => 6,
            'awaiting-seller-transfer' => 7,
            'confirm-transfer' => 8,
            'complete' => 9,
        ];

        return $stepOrder[$this->progress->step_name] ?? 1;
    }
}
