<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\LoanTransfer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransferReceiptService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Store transfer receipt image
     *
     * @throws \Exception
     */
    public function storeTransferReceipt(UploadedFile $file, LoanTransfer $loanTransfer): string
    {
        return DB::transaction(function () use ($file, $loanTransfer) {
            // Store the image
            $imagePath = $this->fileUploadService->storeTransferReceiptImage(
                $file,
                $loanTransfer->seller_id
            );

            // Update loan transfer record
            $loanTransfer->update([
                'transfer_receipt_path' => $imagePath,
            ]);

            Log::info('Transfer receipt stored', [
                'loan_transfer_id' => $loanTransfer->id,
                'auction_id' => $loanTransfer->auction_id,
                'seller_id' => $loanTransfer->seller_id,
                'buyer_id' => $loanTransfer->buyer_id,
                'image_path' => $imagePath,
            ]);

            return $imagePath;
        });
    }

    /**
     * Delete transfer receipt image
     */
    public function deleteTransferReceipt(LoanTransfer $loanTransfer): bool
    {
        return DB::transaction(function () use ($loanTransfer) {
            $deleted = false;

            if ($loanTransfer->transfer_receipt_path) {
                $deleted = $this->fileUploadService->deleteFile($loanTransfer->transfer_receipt_path);
            }

            // Clear the path from database
            $loanTransfer->update(['transfer_receipt_path' => null]);

            Log::info('Transfer receipt deleted', [
                'loan_transfer_id' => $loanTransfer->id,
                'auction_id' => $loanTransfer->auction_id,
                'deleted' => $deleted,
            ]);

            return $deleted;
        });
    }

    /**
     * Get transfer receipt URL
     */
    public function getTransferReceiptUrl(LoanTransfer $loanTransfer): ?string
    {
        if (!$loanTransfer->transfer_receipt_path) {
            return null;
        }

        return $this->fileUploadService->getFileUrl($loanTransfer->transfer_receipt_path);
    }

    /**
     * Check if transfer receipt exists
     */
    public function transferReceiptExists(LoanTransfer $loanTransfer): bool
    {
        if (!$loanTransfer->transfer_receipt_path) {
            return false;
        }

        return $this->fileUploadService->fileExists($loanTransfer->transfer_receipt_path);
    }

    /**
     * Get transfer receipt information
     */
    public function getTransferReceiptInfo(LoanTransfer $loanTransfer): ?array
    {
        if (!$loanTransfer->transfer_receipt_path) {
            return null;
        }

        if (!$this->fileUploadService->fileExists($loanTransfer->transfer_receipt_path)) {
            return null;
        }

        return [
            'path' => $loanTransfer->transfer_receipt_path,
            'url' => $this->fileUploadService->getFileUrl($loanTransfer->transfer_receipt_path),
            'size' => $this->fileUploadService->getFileSize($loanTransfer->transfer_receipt_path),
            'size_mb' => round($this->fileUploadService->getFileSize($loanTransfer->transfer_receipt_path) / 1024 / 1024, 2),
            'mime_type' => $this->fileUploadService->getFileMimeType($loanTransfer->transfer_receipt_path),
            'created_at' => $loanTransfer->updated_at,
        ];
    }

    /**
     * Validate transfer receipt for loan transfer
     */
    public function validateTransferReceipt(LoanTransfer $loanTransfer): array
    {
        $errors = [];

        if (!$loanTransfer->transfer_receipt_path) {
            $errors[] = 'رسید انتقال وام آپلود نشده است.';
            return [
                'valid' => false,
                'errors' => $errors,
            ];
        }

        if (!$this->fileUploadService->fileExists($loanTransfer->transfer_receipt_path)) {
            $errors[] = 'فایل رسید انتقال وام یافت نشد.';
            return [
                'valid' => false,
                'errors' => $errors,
            ];
        }

        // Check file size (should be reasonable for a receipt)
        $fileSize = $this->fileUploadService->getFileSize($loanTransfer->transfer_receipt_path);
        if ($fileSize > 10 * 1024 * 1024) { // 10MB max
            $errors[] = 'حجم فایل رسید انتقال وام بیش از حد مجاز است.';
        }

        if ($fileSize < 10 * 1024) { // 10KB min
            $errors[] = 'حجم فایل رسید انتقال وام کمتر از حد مجاز است.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get all transfer receipts for user
     */
    public function getUserTransferReceipts(User $user): array
    {
        $loanTransfers = LoanTransfer::where('seller_id', $user->id)
            ->whereNotNull('transfer_receipt_path')
            ->with(['auction', 'buyer'])
            ->get();

        $receipts = [];

        foreach ($loanTransfers as $transfer) {
            $receiptInfo = $this->getTransferReceiptInfo($transfer);

            if ($receiptInfo) {
                $receipts[] = [
                    'loan_transfer' => $transfer,
                    'receipt_info' => $receiptInfo,
                    'auction' => $transfer->auction,
                    'buyer' => $transfer->buyer,
                ];
            }
        }

        return $receipts;
    }

    /**
     * Cleanup orphaned transfer receipts
     */
    public function cleanupOrphanedReceipts(): int
    {
        $deletedCount = 0;

        try {
            // Get all transfer receipt files
            $allFiles = Storage::disk('public')->allFiles('transfer-receipts');

            if (empty($allFiles)) {
                return 0;
            }

            // Get all transfer receipt paths from database
            $usedPaths = LoanTransfer::whereNotNull('transfer_receipt_path')
                ->pluck('transfer_receipt_path')
                ->toArray();

            // Find orphaned files (files not referenced in database)
            $orphanedFiles = array_diff($allFiles, $usedPaths);

            foreach ($orphanedFiles as $file) {
                if ($this->fileUploadService->deleteFile($file)) {
                    $deletedCount++;
                }
            }

            Log::info('Cleaned up orphaned transfer receipts', [
                'deleted_count' => $deletedCount,
                'orphaned_files' => count($orphanedFiles),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup orphaned transfer receipts', [
                'error' => $e->getMessage(),
            ]);
        }

        return $deletedCount;
    }
}
