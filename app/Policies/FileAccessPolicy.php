<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class FileAccessPolicy
{
    /**
     * Determine whether the user can view the file.
     */
    public function view(User $user, string $filePath): bool
    {
        // Admin can view all files
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if file belongs to user
        return $this->fileBelongsToUser($filePath, $user->id);
    }

    /**
     * Determine whether the user can download the file.
     */
    public function download(User $user, string $filePath): bool
    {
        // Admin can download all files
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if file belongs to user
        return $this->fileBelongsToUser($filePath, $user->id);
    }

    /**
     * Determine whether the user can delete the file.
     */
    public function delete(User $user, string $filePath): bool
    {
        // Admin can delete all files
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if file belongs to user
        return $this->fileBelongsToUser($filePath, $user->id);
    }

    /**
     * Check if file belongs to user based on path
     */
    protected function fileBelongsToUser(string $filePath, int $userId): bool
    {
        // Extract user ID from path
        // Expected formats:
        // - receipts/{user_id}/filename
        // - transfer-receipts/{user_id}/filename

        $pathParts = explode('/', $filePath);

        if (count($pathParts) < 2) {
            return false;
        }

        $directory = $pathParts[0];
        $pathUserId = $pathParts[1];

        // Check if it's a valid user directory
        if (!in_array($directory, ['receipts', 'transfer-receipts'])) {
            return false;
        }

        // Check if user ID matches
        return (string) $userId === $pathUserId;
    }

    /**
     * Determine whether the user can access receipt images.
     */
    public function viewReceipt(User $user, string $filePath): bool
    {
        return $this->view($user, $filePath);
    }

    /**
     * Determine whether the user can access transfer receipt images.
     */
    public function viewTransferReceipt(User $user, string $filePath): bool
    {
        return $this->view($user, $filePath);
    }

    /**
     * Determine whether the user can upload files.
     */
    public function upload(User $user): bool
    {
        // Only authenticated users can upload files
        return $user !== null;
    }

    /**
     * Determine whether the user can manage storage.
     */
    public function manage(User $user): bool
    {
        // Only admin can manage storage
        return $user->hasRole('admin');
    }
}

