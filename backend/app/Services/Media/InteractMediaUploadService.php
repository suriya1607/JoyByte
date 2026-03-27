<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InteractMediaUploadService
{
    public function uploadUserAvatar(UploadedFile $file, ?string $existingPath = null): string
    {
        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $file->store('avatars', 'public');
    }
}
