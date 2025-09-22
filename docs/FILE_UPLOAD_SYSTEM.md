# File Upload System Documentation

## Overview

The file upload system provides secure, organized, and validated file storage for receipt images and transfer receipts. It includes comprehensive validation, access control, and proper file organization with UUID-based naming.

## Storage Structure

### Directory Organization

```
storage/app/public/
├── receipts/
│   ├── {user_id}/
│   │   ├── receipt_{user_id}_{timestamp}_{uuid}.jpg
│   │   ├── receipt_{user_id}_{timestamp}_{uuid}.png
│   │   └── receipt_{user_id}_{timestamp}_{uuid}.webp
│   └── ...
├── transfer-receipts/
│   ├── {user_id}/
│   │   ├── transfer_{user_id}_{timestamp}_{uuid}.jpg
│   │   ├── transfer_{user_id}_{timestamp}_{uuid}.png
│   │   └── transfer_{user_id}_{timestamp}_{uuid}.webp
│   └── ...
```

### File Naming Convention

**Receipt Images:**

- Format: `receipt_{user_id}_{YYYYMMDD_HHMMSS}_{uuid}.{extension}`
- Example: `receipt_1_20250127_143022_a1b2c3d4-e5f6-7890-abcd-ef1234567890.jpg`

**Transfer Receipt Images:**

- Format: `transfer_{user_id}_{YYYYMMDD_HHMMSS}_{uuid}.{extension}`
- Example: `transfer_1_20250127_143022_a1b2c3d4-e5f6-7890-abcd-ef1234567890.jpg`

## FileUploadService

### Core Methods

#### `storeReceiptImage(UploadedFile $file, int $userId): string`

Stores a receipt image with comprehensive validation.

**Validation Rules:**

- File size: Maximum 5MB
- MIME types: `image/jpeg`, `image/jpg`, `image/png`, `image/webp`
- Extensions: `jpg`, `jpeg`, `png`, `webp`
- Dimensions: Minimum 100x100 pixels
- File integrity: Must be a valid image

**Usage:**

```php
$fileUploadService = app(FileUploadService::class);
$path = $fileUploadService->storeReceiptImage($uploadedFile, $userId);
```

#### `storeTransferReceiptImage(UploadedFile $file, int $userId): string`

Stores a transfer receipt image with the same validation rules.

#### `getFileUrl(string $storagePath): string`

Returns the public URL for a stored file.

```php
$url = $fileUploadService->getFileUrl('receipts/1/receipt_1_20250127_143022_uuid.jpg');
// Returns: http://localhost/storage/receipts/1/receipt_1_20250127_143022_uuid.jpg
```

#### `deleteFile(string $storagePath): bool`

Deletes a stored file and returns success status.

#### `fileExists(string $storagePath): bool`

Checks if a file exists in storage.

#### `getFileSize(string $storagePath): int`

Returns file size in bytes.

#### `getFileMimeType(string $storagePath): string`

Returns the MIME type of the file.

### Utility Methods

#### `getStorageStats(): array`

Returns comprehensive storage statistics.

```php
$stats = $fileUploadService->getStorageStats();
/*
Returns:
[
    'receipts' => [
        'count' => 150,
        'total_size' => 52428800,
        'total_size_mb' => 50.0
    ],
    'transfer_receipts' => [
        'count' => 75,
        'total_size' => 26214400,
        'total_size_mb' => 25.0
    ],
    'total' => [
        'count' => 225,
        'total_size' => 78643200,
        'total_size_mb' => 75.0
    ]
]
*/
```

#### `cleanupOldFiles(int $userId, int $daysOld = 30): int`

Cleans up old files for a specific user.

## Access Control

### FileAccessPolicy

The `FileAccessPolicy` ensures users can only access their own files or admin users can access all files.

#### Policy Methods

**`view(User $user, string $filePath): bool`**

- Admin: Can view all files
- User: Can only view their own files

**`download(User $user, string $filePath): bool`**

- Admin: Can download all files
- User: Can only download their own files

**`delete(User $user, string $filePath): bool`**

- Admin: Can delete all files
- User: Can only delete their own files

#### File Path Validation

The policy extracts user ID from file paths:

- `receipts/{user_id}/filename` → User ID = `{user_id}`
- `transfer-receipts/{user_id}/filename` → User ID = `{user_id}`

### Authorization Gates

```php
// Check if user can view file
if (Gate::allows('view', $filePath)) {
    // Show file
}

// Check if user can download file
if (Gate::allows('download', $filePath)) {
    // Allow download
}

// Check if user can delete file
if (Gate::allows('delete', $filePath)) {
    // Allow deletion
}
```

## File Controller

### Routes

**File Display:**

- `GET /receipts/{path}` - Display receipt image
- `GET /transfer-receipts/{path}` - Display transfer receipt image

**File Management:**

- `GET /files/{type}/{path}` - Download file
- `GET /files/{type}/{path}/info` - Get file information
- `DELETE /files/{type}/{path}` - Delete file

**Admin Routes:**

- `GET /admin/files/stats` - Get storage statistics
- `POST /admin/files/cleanup` - Cleanup old files

### Controller Methods

#### `showReceipt(Request $request, string $path)`

Displays receipt images with proper authorization and error handling.

#### `showTransferReceipt(Request $request, string $path)`

Displays transfer receipt images with proper authorization.

#### `download(Request $request, string $type, string $path)`

Downloads files with appropriate headers and authorization.

#### `delete(Request $request, string $type, string $path)`

Deletes files after authorization check.

## Blade Components

### Receipt Image Component

```blade
<x-receipt-image
    :path="$receipt->receipt_image_path"
    alt="رسید پرداخت"
    class="max-w-full h-auto rounded-lg"
/>
```

**Features:**

- Automatic error handling for missing images
- Fallback display for broken images
- Responsive design
- RTL support

### Transfer Receipt Image Component

```blade
<x-transfer-receipt-image
    :loan-transfer="$loanTransfer"
    alt="رسید انتقال وام"
    class="max-w-full h-auto rounded-lg"
/>
```

**Features:**

- Delete button for authorized users
- Automatic error handling
- Integration with loan transfer data

### File Upload Component

```blade
<x-file-upload
    name="receipt_image"
    label="آپلود رسید پرداخت"
    accept="image/jpeg,image/jpg,image/png,image/webp"
    max-size="5MB"
    :required="true"
    :preview="true"
/>
```

**Features:**

- Drag and drop support
- File validation with real-time feedback
- Image preview
- Persian error messages
- File size and type validation

## Validation Rules

### File Size

- Maximum: 5MB (5,242,880 bytes)
- Minimum: 10KB (10,240 bytes) for transfer receipts

### File Types

- **Allowed MIME Types:**

  - `image/jpeg`
  - `image/jpg`
  - `image/png`
  - `image/webp`

- **Allowed Extensions:**
  - `.jpg`
  - `.jpeg`
  - `.png`
  - `.webp`

### Image Dimensions

- Minimum width: 100 pixels
- Minimum height: 100 pixels
- Uses `getimagesize()` for validation

### Security Validation

- File integrity check using `getimagesize()`
- MIME type validation
- Extension validation
- File size validation

## Error Handling

### Validation Errors

**Persian Error Messages:**

- "حجم فایل نباید بیشتر از 5 مگابایت باشد." - File too large
- "فرمت فایل مجاز نیست. فقط فایل‌های JPG، PNG و WebP قابل قبول هستند." - Invalid format
- "ابعاد تصویر باید حداقل 100x100 پیکسل باشد." - Dimensions too small
- "فایل آپلود شده یک تصویر معتبر نیست." - Invalid image file

### Exception Handling

```php
try {
    $path = $fileUploadService->storeReceiptImage($file, $userId);
} catch (\Exception $e) {
    // Handle error with Persian message
    return response()->json([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
```

### Logging

All file operations are logged with context:

```php
Log::info('Receipt image stored successfully', [
    'user_id' => $userId,
    'filename' => $filename,
    'storage_path' => $storedPath,
    'file_size' => $file->getSize(),
    'mime_type' => $file->getMimeType(),
]);
```

## Security Features

### File Isolation

- Files are organized by user ID
- Users cannot access other users' files
- Admin users have full access

### Input Validation

- Comprehensive file type validation
- File size limits
- Image dimension validation
- File integrity checks

### Access Control

- Policy-based authorization
- Route-level protection
- Gate-based permissions

### File Naming

- UUID-based unique naming
- Timestamp inclusion
- User ID prefix
- Sanitized original names

## Performance Considerations

### Storage Optimization

- Efficient directory structure
- UUID-based naming prevents conflicts
- Automatic cleanup of old files

### Caching

- File URLs are cacheable
- Storage statistics can be cached
- Image serving with proper cache headers

### Database Efficiency

- File paths stored in database
- Quick lookup by user ID
- Indexed file operations

## Testing

### Test Coverage

**FileUploadTest** covers:

- Valid file uploads
- File size validation
- File type validation
- Image dimension validation
- Unique filename generation
- File URL generation
- File deletion
- File existence checks
- Storage statistics
- Multiple image formats
- Filename sanitization

### Test Examples

```php
/** @test */
public function it_can_store_receipt_image_with_valid_file()
{
    $file = UploadedFile::fake()->image('receipt.jpg', 800, 600)->size(1024);
    $userId = 1;

    $path = $this->fileUploadService->storeReceiptImage($file, $userId);

    $this->assertNotNull($path);
    $this->assertStringStartsWith('receipts/1/', $path);
    Storage::disk('public')->assertExists($path);
}
```

## Deployment

### Storage Link

Create symbolic link for public access:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

### Directory Permissions

Ensure proper permissions:

```bash
chmod -R 755 storage/app/public
chown -R www-data:www-data storage/app/public
```

### Environment Configuration

```env
# File storage configuration
FILESYSTEM_DISK=public
STORAGE_LINK_CREATED=true

# File upload limits
UPLOAD_MAX_FILESIZE=5M
POST_MAX_SIZE=10M
```

## Monitoring

### Storage Statistics

Monitor storage usage:

```php
$stats = $fileUploadService->getStorageStats();
// Returns detailed statistics about file storage
```

### Cleanup Operations

Regular cleanup of old files:

```php
$deletedCount = $fileUploadService->cleanupOldFiles($userId, 30);
// Deletes files older than 30 days
```

### Error Monitoring

Monitor file upload errors:

```php
Log::error('Failed to store receipt image', [
    'user_id' => $userId,
    'error' => $e->getMessage(),
    'filename' => $file->getClientOriginalName(),
    'file_size' => $file->getSize(),
]);
```

## Best Practices

1. **Always validate files** before storage
2. **Use UUID-based naming** to prevent conflicts
3. **Implement proper access control** with policies
4. **Log all file operations** for audit trails
5. **Clean up old files** regularly
6. **Monitor storage usage** to prevent disk space issues
7. **Use proper error handling** with user-friendly messages
8. **Test file uploads thoroughly** with various file types and sizes
9. **Implement proper caching** for file URLs
10. **Use secure file serving** with proper headers

