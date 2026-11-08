<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * The single write path for admin catalog entries (category, bean, method,
 * recipe). It owns the derived slug and the image lifecycle so the order is
 * the same everywhere: upload/persist first, delete the old asset after.
 */
class CatalogWriter
{
    public function __construct(
        private CloudinaryUploader $uploader
    ) {}

    /**
     * Create or update an entry. A new image is uploaded before the row is
     * written, so a failed upload leaves the existing row untouched.
     */
    public function save(Model $model, array $data, ?UploadedFile $image = null, string $folder = 'coffee-app'): void
    {
        $data['slug'] = Str::slug((string) ($data['name'] ?? $data['title']));

        if ($image) {
            $oldPublicId = $model->getAttribute('image_public_id');

            $result = $oldPublicId
                ? $this->uploader->replace($image, $oldPublicId, $folder)
                : $this->uploader->upload($image, $folder);

            $data['image_url'] = $result['secure_url'];
            $data['image_public_id'] = $result['public_id'];
        }

        $model->fill($data)->save();
    }

    /**
     * Delete the row first: a foreign key that blocks it leaves the image in
     * place. Once the row is gone, an image failure only orphans the asset.
     */
    public function delete(Model $model): void
    {
        $publicId = $model->getAttribute('image_public_id');

        $model->delete();

        try {
            $this->uploader->delete($publicId);
        } catch (\Throwable $e) {
            Log::warning('Could not delete catalog image after row delete', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
