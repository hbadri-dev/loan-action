<?php

namespace Tests\Feature;

use App\Services\FileUploadService;
use App\Services\ReceiptService;
use App\Services\TransferReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected FileUploadService $fileUploadService;
    protected ReceiptService $receiptService;
    protected TransferReceiptService $transferReceiptService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->fileUploadService = new FileUploadService();
        $this->receiptService = new ReceiptService($this->fileUploadService);
        $this->transferReceiptService = new TransferReceiptService($this->fileUploadService);
    }

    /** @test */
    public function it_can_store_receipt_image_with_valid_file()
    {
        $file = UploadedFile::fake()->image('receipt.jpg', 800, 600)->size(1024);
        $userId = 1;

        $path = $this->fileUploadService->storeReceiptImage($file, $userId);

        $this->assertNotNull($path);
        $this->assertStringStartsWith('receipts/1/', $path);
        $this->assertStringEndsWith('.jpg', $path);

        Storage::disk('public')->assertExists($path);
    }

    /** @test */
    public function it_rejects_files_larger_than_5mb()
    {
        $file = UploadedFile::fake()->image('large.jpg')->size(6 * 1024 * 1024); // 6MB
        $userId = 1;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('حجم فایل نباید بیشتر از 5 مگابایت باشد.');

        $this->fileUploadService->storeReceiptImage($file, $userId);
    }

    /** @test */
    public function it_rejects_invalid_file_types()
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024);
        $userId = 1;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('فرمت فایل مجاز نیست.');

        $this->fileUploadService->storeReceiptImage($file, $userId);
    }

    /** @test */
    public function it_validates_image_dimensions()
    {
        $file = UploadedFile::fake()->image('small.jpg', 50, 50); // Too small
        $userId = 1;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ابعاد تصویر باید حداقل');

        $this->fileUploadService->storeReceiptImage($file, $userId);
    }

    /** @test */
    public function it_generates_unique_filenames()
    {
        $file1 = UploadedFile::fake()->image('receipt1.jpg');
        $file2 = UploadedFile::fake()->image('receipt2.jpg');
        $userId = 1;

        $path1 = $this->fileUploadService->storeReceiptImage($file1, $userId);
        $path2 = $this->fileUploadService->storeReceiptImage($file2, $userId);

        $this->assertNotEquals($path1, $path2);

        // Extract filenames from paths
        $filename1 = basename($path1);
        $filename2 = basename($path2);

        $this->assertNotEquals($filename1, $filename2);
        $this->assertStringStartsWith('receipt_1_', $filename1);
        $this->assertStringStartsWith('receipt_1_', $filename2);
    }

    /** @test */
    public function it_can_get_file_url()
    {
        $file = UploadedFile::fake()->image('receipt.jpg');
        $userId = 1;

        $path = $this->fileUploadService->storeReceiptImage($file, $userId);
        $url = $this->fileUploadService->getFileUrl($path);

        $this->assertStringStartsWith('http', $url);
        $this->assertStringContainsString('storage', $url);
        $this->assertStringContainsString(basename($path), $url);
    }

    /** @test */
    public function it_can_delete_files()
    {
        $file = UploadedFile::fake()->image('receipt.jpg');
        $userId = 1;

        $path = $this->fileUploadService->storeReceiptImage($file, $userId);

        Storage::disk('public')->assertExists($path);

        $deleted = $this->fileUploadService->deleteFile($path);

        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($path);
    }

    /** @test */
    public function it_can_check_file_existence()
    {
        $file = UploadedFile::fake()->image('receipt.jpg');
        $userId = 1;

        $path = $this->fileUploadService->storeReceiptImage($file, $userId);

        $this->assertTrue($this->fileUploadService->fileExists($path));
        $this->assertFalse($this->fileUploadService->fileExists('nonexistent/file.jpg'));
    }

    /** @test */
    public function it_can_get_file_size_and_mime_type()
    {
        $file = UploadedFile::fake()->image('receipt.jpg', 800, 600)->size(2048);
        $userId = 1;

        $path = $this->fileUploadService->storeReceiptImage($file, $userId);

        $size = $this->fileUploadService->getFileSize($path);
        $mimeType = $this->fileUploadService->getFileMimeType($path);

        $this->assertGreaterThan(0, $size);
        $this->assertStringStartsWith('image/', $mimeType);
    }

    /** @test */
    public function it_can_store_transfer_receipt_image()
    {
        $file = UploadedFile::fake()->image('transfer.jpg', 800, 600);
        $userId = 1;

        $path = $this->fileUploadService->storeTransferReceiptImage($file, $userId);

        $this->assertNotNull($path);
        $this->assertStringStartsWith('transfer-receipts/1/', $path);
        $this->assertStringEndsWith('.jpg', $path);

        Storage::disk('public')->assertExists($path);
    }

    /** @test */
    public function it_can_get_storage_statistics()
    {
        // Upload some files
        $receipt1 = UploadedFile::fake()->image('receipt1.jpg')->size(1024);
        $receipt2 = UploadedFile::fake()->image('receipt2.jpg')->size(2048);
        $transfer = UploadedFile::fake()->image('transfer.jpg')->size(1536);

        $this->fileUploadService->storeReceiptImage($receipt1, 1);
        $this->fileUploadService->storeReceiptImage($receipt2, 2);
        $this->fileUploadService->storeTransferReceiptImage($transfer, 1);

        $stats = $this->fileUploadService->getStorageStats();

        $this->assertArrayHasKey('receipts', $stats);
        $this->assertArrayHasKey('transfer_receipts', $stats);
        $this->assertArrayHasKey('total', $stats);

        $this->assertEquals(2, $stats['receipts']['count']);
        $this->assertEquals(1, $stats['transfer_receipts']['count']);
        $this->assertEquals(3, $stats['total']['count']);

        $this->assertGreaterThan(0, $stats['total']['total_size']);
    }

    /** @test */
    public function it_can_cleanup_old_files()
    {
        // This test would require mocking timestamps
        // For now, just test that the method runs without error
        $deletedCount = $this->fileUploadService->cleanupOldFiles(1, 30);

        $this->assertIsInt($deletedCount);
        $this->assertGreaterThanOrEqual(0, $deletedCount);
    }

    /** @test */
    public function it_sanitizes_filenames()
    {
        $file = UploadedFile::fake()->image('receipt with spaces & special chars!.jpg');
        $userId = 1;

        $path = $this->fileUploadService->storeReceiptImage($file, $userId);
        $filename = basename($path);

        // Should not contain spaces or special characters
        $this->assertStringNotContainsString(' ', $filename);
        $this->assertStringNotContainsString('&', $filename);
        $this->assertStringNotContainsString('!', $filename);

        // Should contain UUID and timestamp
        $this->assertStringStartsWith('receipt_1_', $filename);
        $this->assertStringEndsWith('.jpg', $filename);
    }

    /** @test */
    public function it_handles_different_image_formats()
    {
        $formats = ['jpg', 'jpeg', 'png', 'webp'];

        foreach ($formats as $format) {
            $file = UploadedFile::fake()->image("receipt.{$format}");
            $userId = 1;

            $path = $this->fileUploadService->storeReceiptImage($file, $userId);

            $this->assertNotNull($path);
            $this->assertStringEndsWith(".{$format}", $path);
            Storage::disk('public')->assertExists($path);
        }
    }
}

