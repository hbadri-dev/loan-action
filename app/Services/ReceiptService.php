<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Events\ReceiptApproved;
use App\Events\ReceiptRejected;
use App\Models\Auction;
use App\Models\PaymentReceipt;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReceiptService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    /**
     * Create a pending payment receipt
     *
     * @throws \Exception
     */
    public function createPendingReceipt(
        User $user,
        Auction $auction,
        PaymentType $type,
        int $amount,
        string $imagePath
    ): PaymentReceipt {
        return DB::transaction(function () use ($user, $auction, $type, $amount, $imagePath) {
            // Validate amount based on type
            $this->validateAmount($type, $amount);

            // Check if user already has a pending receipt for this type and auction
            $existingReceipt = PaymentReceipt::where('user_id', $user->id)
                ->where('auction_id', $auction->id)
                ->where('type', $type)
                ->where('status', PaymentStatus::PENDING_REVIEW)
                ->first();

            if ($existingReceipt) {
                // Update existing receipt
                $existingReceipt->update([
                    'amount' => $amount,
                    'receipt_image_path' => $imagePath,
                    'status' => PaymentStatus::PENDING_REVIEW,
                    'reject_reason' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                ]);

                Log::info('Payment receipt updated', [
                    'receipt_id' => $existingReceipt->id,
                    'user_id' => $user->id,
                    'auction_id' => $auction->id,
                    'type' => $type->value,
                    'amount' => $amount,
                ]);

                return $existingReceipt->fresh();
            } else {
                // Create new receipt
                $receipt = PaymentReceipt::create([
                    'user_id' => $user->id,
                    'auction_id' => $auction->id,
                    'type' => $type,
                    'amount' => $amount,
                    'receipt_image_path' => $imagePath,
                    'status' => PaymentStatus::PENDING_REVIEW,
                ]);

                Log::info('Payment receipt created', [
                    'receipt_id' => $receipt->id,
                    'user_id' => $user->id,
                    'auction_id' => $auction->id,
                    'type' => $type->value,
                    'amount' => $amount,
                ]);

                return $receipt;
            }
        });
    }

    /**
     * Store uploaded receipt image
     *
     * @throws \Exception
     */
    public function storeReceiptImage(UploadedFile $file, int $userId): string
    {
        return $this->fileUploadService->storeReceiptImage($file, $userId);
    }

    /**
     * Approve a payment receipt
     *
     * @throws \Exception
     */
    public function approveReceipt(PaymentReceipt $receipt, User $reviewer): PaymentReceipt
    {
        return DB::transaction(function () use ($receipt, $reviewer) {
            $receipt = PaymentReceipt::lockForUpdate()->findOrFail($receipt->id);

            if ($receipt->status !== PaymentStatus::PENDING_REVIEW) {
                throw new \Exception('فقط رسیدهای در انتظار بررسی قابل تأیید هستند.');
            }

            $receipt->update([
                'status' => PaymentStatus::APPROVED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'reject_reason' => null,
            ]);

            // Fire receipt approved event
            event(new ReceiptApproved($receipt, $reviewer));

            Log::info('Payment receipt approved', [
                'receipt_id' => $receipt->id,
                'reviewer_id' => $reviewer->id,
                'type' => $receipt->type->value,
                'amount' => $receipt->amount,
            ]);

            return $receipt->fresh();
        });
    }

    /**
     * Reject a payment receipt
     *
     * @throws \Exception
     */
    public function rejectReceipt(PaymentReceipt $receipt, User $reviewer, string $reason): PaymentReceipt
    {
        return DB::transaction(function () use ($receipt, $reviewer, $reason) {
            $receipt = PaymentReceipt::lockForUpdate()->findOrFail($receipt->id);

            if ($receipt->status !== PaymentStatus::PENDING_REVIEW) {
                throw new \Exception('فقط رسیدهای در انتظار بررسی قابل رد هستند.');
            }

            $receipt->update([
                'status' => PaymentStatus::REJECTED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'reject_reason' => $reason,
            ]);

            // Fire receipt rejected event
            event(new ReceiptRejected($receipt, $reviewer, $reason));

            Log::info('Payment receipt rejected', [
                'receipt_id' => $receipt->id,
                'reviewer_id' => $reviewer->id,
                'type' => $receipt->type->value,
                'amount' => $receipt->amount,
                'reason' => $reason,
            ]);

            return $receipt->fresh();
        });
    }

    /**
     * Get user's receipts for an auction
     */
    public function getUserReceipts(User $user, Auction $auction): \Illuminate\Database\Eloquent\Collection
    {
        return PaymentReceipt::where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if user has approved receipt for specific type and auction
     */
    public function hasApprovedReceipt(User $user, Auction $auction, PaymentType $type): bool
    {
        return PaymentReceipt::where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->where('type', $type)
            ->where('status', PaymentStatus::APPROVED)
            ->exists();
    }

    /**
     * Validate amount based on payment type
     *
     * @throws \Exception
     */
    private function validateAmount(PaymentType $type, int $amount): void
    {
        switch ($type) {
            case PaymentType::BUYER_FEE:
            case PaymentType::SELLER_FEE:
                if ($amount !== 3000000) {
                    throw new \Exception('مبلغ کارمزد باید دقیقاً 3,000,000 تومان باشد.');
                }
                break;
            case PaymentType::BUYER_PURCHASE_AMOUNT:
                if ($amount <= 0) {
                    throw new \Exception('مبلغ خرید باید بیشتر از صفر باشد.');
                }
                break;
        }
    }

    /**
     * Delete receipt image file
     */
    public function deleteReceiptImage(string $imagePath): bool
    {
        return $this->fileUploadService->deleteFile($imagePath);
    }

    /**
     * Get receipt statistics
     */
    public function getReceiptStats(): array
    {
        return [
            'pending' => PaymentReceipt::where('status', PaymentStatus::PENDING_REVIEW)->count(),
            'approved' => PaymentReceipt::where('status', PaymentStatus::APPROVED)->count(),
            'rejected' => PaymentReceipt::where('status', PaymentStatus::REJECTED)->count(),
            'total' => PaymentReceipt::count(),
        ];
    }
}
