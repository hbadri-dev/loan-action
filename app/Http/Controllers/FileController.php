<?php

namespace App\Http\Controllers;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class FileController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display receipt image
     */
    public function showReceipt(Request $request, string $path)
    {
        $fullPath = "receipts/{$path}";

        // Check authorization
        if (!Gate::allows('viewReceipt', $fullPath)) {
            abort(403, 'شما مجاز به مشاهده این فایل نیستید.');
        }

        // Check if file exists
        if (!$this->fileUploadService->fileExists($fullPath)) {
            abort(404, 'فایل یافت نشد.');
        }

        return $this->serveFile($fullPath);
    }

    /**
     * Display transfer receipt image
     */
    public function showTransferReceipt(Request $request, string $path)
    {
        $fullPath = "transfer-receipts/{$path}";

        // Check authorization
        if (!Gate::allows('viewTransferReceipt', $fullPath)) {
            abort(403, 'شما مجاز به مشاهده این فایل نیستید.');
        }

        // Check if file exists
        if (!$this->fileUploadService->fileExists($fullPath)) {
            abort(404, 'فایل یافت نشد.');
        }

        return $this->serveFile($fullPath);
    }

    /**
     * Download file
     */
    public function download(Request $request, string $type, string $path)
    {
        $fullPath = "{$type}/{$path}";

        // Check authorization
        if (!Gate::allows('download', $fullPath)) {
            abort(403, 'شما مجاز به دانلود این فایل نیستید.');
        }

        // Check if file exists
        if (!$this->fileUploadService->fileExists($fullPath)) {
            abort(404, 'فایل یافت نشد.');
        }

        return $this->serveFile($fullPath, true);
    }

    /**
     * Delete file
     */
    public function delete(Request $request, string $type, string $path)
    {
        $fullPath = "{$type}/{$path}";

        // Check authorization
        if (!Gate::allows('delete', $fullPath)) {
            abort(403, 'شما مجاز به حذف این فایل نیستید.');
        }

        // Check if file exists
        if (!$this->fileUploadService->fileExists($fullPath)) {
            abort(404, 'فایل یافت نشد.');
        }

        // Delete file
        if ($this->fileUploadService->deleteFile($fullPath)) {
            return response()->json([
                'success' => true,
                'message' => 'فایل با موفقیت حذف شد.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'خطا در حذف فایل.',
        ], 500);
    }

    /**
     * Get file information
     */
    public function info(Request $request, string $type, string $path)
    {
        $fullPath = "{$type}/{$path}";

        // Check authorization
        if (!Gate::allows('view', $fullPath)) {
            abort(403, 'شما مجاز به مشاهده اطلاعات این فایل نیستید.');
        }

        // Check if file exists
        if (!$this->fileUploadService->fileExists($fullPath)) {
            abort(404, 'فایل یافت نشد.');
        }

        return response()->json([
            'success' => true,
            'file_info' => [
                'path' => $fullPath,
                'url' => $this->fileUploadService->getFileUrl($fullPath),
                'size' => $this->fileUploadService->getFileSize($fullPath),
                'size_mb' => round($this->fileUploadService->getFileSize($fullPath) / 1024 / 1024, 2),
                'mime_type' => Storage::disk('public')->mimeType($fullPath),
                'last_modified' => Storage::disk('public')->lastModified($fullPath),
            ],
        ]);
    }

    /**
     * Get storage statistics (admin only)
     */
    public function stats(Request $request)
    {
        // Check authorization
        if (!Gate::allows('manage')) {
            abort(403, 'شما مجاز به مشاهده آمار ذخیره‌سازی نیستید.');
        }

        $stats = $this->fileUploadService->getStorageStats();

        return response()->json([
            'success' => true,
            'storage_stats' => $stats,
        ]);
    }

    /**
     * Cleanup old files (admin only)
     */
    public function cleanup(Request $request)
    {
        // Check authorization
        if (!Gate::allows('manage')) {
            abort(403, 'شما مجاز به پاکسازی فایل‌ها نیستید.');
        }

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'days_old' => 'integer|min:1|max:365',
        ]);

        $userId = $request->user_id;
        $daysOld = $request->input('days_old', 30);

        $deletedCount = $this->fileUploadService->cleanupOldFiles($userId, $daysOld);

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} فایل قدیمی حذف شد.",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Serve file with proper headers
     */
    protected function serveFile(string $path, bool $download = false)
    {
        $filePath = Storage::disk('public')->path($path);

        if (!file_exists($filePath)) {
            abort(404, 'فایل یافت نشد.');
        }

        $mimeType = Storage::disk('public')->mimeType($path);
        $filename = basename($path);

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $download ? "attachment; filename=\"{$filename}\"" : "inline; filename=\"{$filename}\"",
            'Cache-Control' => 'public, max-age=3600', // Cache for 1 hour
        ];

        return response()->file($filePath, $headers);
    }
}
