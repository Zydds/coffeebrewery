<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryUploader
{
    /**
     * Upload an image to Cloudinary.
     *
     * @return array{secure_url: string, public_id: string}
     */
    public function upload(UploadedFile $file, string $folder = 'coffee-app'): array
    {
        $result = Cloudinary::uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
        ]);

        return [
            'secure_url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    /**
     * Replace an existing image: delete old, upload new.
     *
     * @return array{secure_url: string, public_id: string}
     */
    public function replace(UploadedFile $file, ?string $oldPublicId, string $folder = 'coffee-app'): array
    {
        if ($oldPublicId) {
            $this->delete($oldPublicId);
        }

        return $this->upload($file, $folder);
    }

    /**
     * Delete an image from Cloudinary by public_id.
     */
    public function delete(?string $publicId): void
    {
        if ($publicId) {
            Cloudinary::uploadApi()->destroy($publicId);
        }
    }
}
