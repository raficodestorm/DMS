<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadHelper
{
    /**
     * Upload a file directly to public/uploads/{folder}.
     * Returns the relative path stored in DB (e.g. "uploads/profile_photos/abc123.jpg").
     */
    protected function uploadFile(UploadedFile $file, string $folder): string
    {
        $dir = public_path("uploads/{$folder}");

        // Create directory if it does not exist
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return "uploads/{$folder}/{$filename}";
    }

    /**
     * Delete a file from public/ using the relative path stored in DB.
     */
    protected function deleteFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        $fullPath = public_path($relativePath);

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
