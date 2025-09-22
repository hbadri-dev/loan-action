<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadService
{
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];

    protected array $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'webp',
    ];

    protected int $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes

    protected int $minWidth = 100;
    protected int $minHeight = 100;

    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Store uploaded receipt image with validation
     *
     * @throws \Exception
     */
    public function storeReceiptImage(UploadedFile $file, int $userId): string
    {
        try {
            // Validate file
            $this->validateFile($file);

            // Generate unique filename
            $filename = $this->generateFilename($file, $userId);

            // Get storage path
            $storagePath = $this->getStoragePath($userId, $filename);

            // Validate image dimensions if possible
            $this->validateImageDimensions($file);

            // Store file
            $storedPath = $file->storeAs(
                $this->getDirectoryPath($userId),
                $filename,
                'public'
            );

            if (!$storedPath) {
                throw new \Exception('خطا در ذخیره فایل.');
            }

            Log::info('Receipt image stored successfully', [
                'user_id' => $userId,
                'filename' => $filename,
                'storage_path' => $storedPath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            return $storedPath;

        } catch (\Exception $e) {
            Log::error('Failed to store receipt image', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);

            throw new \Exception('خطا در آپلود فایل: ' . $e->getMessage());
        }
    }

    /**
     * Store uploaded transfer receipt image
     *
     * @throws \Exception
     */
    public function storeTransferReceiptImage(UploadedFile $file, int $userId): string
    {
        try {
            // Validate file
            $this->validateFile($file);

            // Generate unique filename
            $filename = $this->generateFilename($file, $userId, 'transfer');

            // Validate image dimensions if possible
            $this->validateImageDimensions($file);

            // Store file
            $storedPath = $file->storeAs(
                $this->getTransferDirectoryPath($userId),
                $filename,
                'public'
            );

            if (!$storedPath) {
                throw new \Exception('خطا در ذخیره فایل.');
            }

            Log::info('Transfer receipt image stored successfully', [
                'user_id' => $userId,
                'filename' => $filename,
                'storage_path' => $storedPath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            return $storedPath;

        } catch (\Exception $e) {
            Log::error('Failed to store transfer receipt image', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);

            throw new \Exception('خطا در آپلود فایل انتقال: ' . $e->getMessage());
        }
    }

    /**
     * Validate uploaded file
     *
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            throw new \Exception('حجم فایل نباید بیشتر از 5 مگابایت باشد.');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Exception('فرمت فایل مجاز نیست. فقط فایل‌های JPG، PNG و WebP قابل قبول هستند.');
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \Exception('پسوند فایل مجاز نیست. فقط فایل‌های JPG، PNG و WebP قابل قبول هستند.');
        }

        // Check if file is actually an image
        if (!getimagesize($file->getPathname())) {
            throw new \Exception('فایل آپلود شده یک تصویر معتبر نیست.');
        }
    }

    /**
     * Validate image dimensions
     *
     * @throws \Exception
     */
    protected function validateImageDimensions(UploadedFile $file): void
    {
        try {
            $imageInfo = getimagesize($file->getPathname());

            if (!$imageInfo) {
                throw new \Exception('نمی‌توان ابعاد تصویر را تعیین کرد.');
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            if ($width < $this->minWidth || $height < $this->minHeight) {
                throw new \Exception(
                    "ابعاد تصویر باید حداقل {$this->minWidth}x{$this->minHeight} پیکسل باشد."
                );
            }

        } catch (\Exception $e) {
            if ($e->getMessage() === 'نمی‌توان ابعاد تصویر را تعیین کرد.' ||
                strpos($e->getMessage(), 'ابعاد تصویر') === 0) {
                throw $e;
            }

            // If it's another error, just log it but don't fail
            Log::warning('Could not validate image dimensions', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
        }
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file, int $userId, string $prefix = 'receipt'): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $uuid = Str::uuid()->toString();
        $timestamp = now()->format('Ymd_His');

        return "{$prefix}_{$userId}_{$timestamp}_{$uuid}.{$extension}";
    }

    /**
     * Get storage directory path for user
     */
    protected function getDirectoryPath(int $userId): string
    {
        return "receipts/{$userId}";
    }

    /**
     * Get transfer receipt directory path for user
     */
    protected function getTransferDirectoryPath(int $userId): string
    {
        return "transfer-receipts/{$userId}";
    }

    /**
     * Get full storage path
     */
    protected function getStoragePath(int $userId, string $filename): string
    {
        return $this->getDirectoryPath($userId) . '/' . $filename;
    }

    /**
     * Get public URL for stored file
     */
    public function getFileUrl(string $storagePath): string
    {
        return asset('storage/' . $storagePath);
    }

    /**
     * Delete stored file
     */
    public function deleteFile(string $storagePath): bool
    {
        try {
            $deleted = Storage::disk('public')->delete($storagePath);

            if ($deleted) {
                Log::info('File deleted successfully', [
                    'storage_path' => $storagePath,
                ]);
            }

            return $deleted;
        } catch (\Exception $e) {
            Log::error('Failed to delete file', [
                'storage_path' => $storagePath,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $storagePath): bool
    {
        return Storage::disk('public')->exists($storagePath);
    }

    /**
     * Get file size
     */
    public function getFileSize(string $storagePath): int
    {
        return Storage::disk('public')->size($storagePath);
    }

    /**
     * Get file MIME type
     */
    public function getFileMimeType(string $storagePath): string
    {
        return mime_content_type(Storage::disk('public')->path($storagePath));
    }

    /**
     * Clean up old files for user
     */
    public function cleanupOldFiles(int $userId, int $daysOld = 30): int
    {
        try {
            $receiptsPath = $this->getDirectoryPath($userId);
            $transferPath = $this->getTransferDirectoryPath($userId);

            $deletedCount = 0;
            $cutoffDate = now()->subDays($daysOld);

            // Clean up receipt files
            $files = Storage::disk('public')->files($receiptsPath);
            foreach ($files as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);
                if ($lastModified < $cutoffDate->timestamp) {
                    if (Storage::disk('public')->delete($file)) {
                        $deletedCount++;
                    }
                }
            }

            // Clean up transfer receipt files
            $files = Storage::disk('public')->files($transferPath);
            foreach ($files as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);
                if ($lastModified < $cutoffDate->timestamp) {
                    if (Storage::disk('public')->delete($file)) {
                        $deletedCount++;
                    }
                }
            }

            Log::info('Cleaned up old files', [
                'user_id' => $userId,
                'deleted_count' => $deletedCount,
                'days_old' => $daysOld,
            ]);

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('Failed to cleanup old files', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(): array
    {
        try {
            $receiptsSize = 0;
            $transferSize = 0;
            $receiptsCount = 0;
            $transferCount = 0;

            // Count receipt files
            $receiptFiles = Storage::disk('public')->allFiles('receipts');
            foreach ($receiptFiles as $file) {
                $receiptsSize += Storage::disk('public')->size($file);
                $receiptsCount++;
            }

            // Count transfer receipt files
            $transferFiles = Storage::disk('public')->allFiles('transfer-receipts');
            foreach ($transferFiles as $file) {
                $transferSize += Storage::disk('public')->size($file);
                $transferCount++;
            }

            return [
                'receipts' => [
                    'count' => $receiptsCount,
                    'total_size' => $receiptsSize,
                    'total_size_mb' => round($receiptsSize / 1024 / 1024, 2),
                ],
                'transfer_receipts' => [
                    'count' => $transferCount,
                    'total_size' => $transferSize,
                    'total_size_mb' => round($transferSize / 1024 / 1024, 2),
                ],
                'total' => [
                    'count' => $receiptsCount + $transferCount,
                    'total_size' => $receiptsSize + $transferSize,
                    'total_size_mb' => round(($receiptsSize + $transferSize) / 1024 / 1024, 2),
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get storage stats', [
                'error' => $e->getMessage(),
            ]);

            return [
                'receipts' => ['count' => 0, 'total_size' => 0, 'total_size_mb' => 0],
                'transfer_receipts' => ['count' => 0, 'total_size' => 0, 'total_size_mb' => 0],
                'total' => ['count' => 0, 'total_size' => 0, 'total_size_mb' => 0],
            ];
        }
    }
}
