<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected $cloudinary;
    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => ['cloud_name'=>env('CLOUDINARY_CLOUD_NAME')],
            'api' => ['key'=>env('CLOUDINARY_API_KEY'),'secret'=>env('CLOUDINARY_API_SECRET')]
        ]);
    }

    public function upload($path, $options = [])
    {
        return $this->cloudinary->uploadApi()->upload($path, $options);
    }

    public function uploadFile(UploadedFile $file, array $options = [])
    {
        return $this->upload($file->getRealPath(), array_merge([
            'folder' => 'meditrust/products',
            'resource_type' => 'image',
        ], $options));
    }

    public function uploadMany(array $files, array $options = []): array
    {
        return array_values(array_filter(array_map(function ($file) use ($options) {
            if ($file instanceof UploadedFile) {
                $result = $this->uploadFile($file, $options);
                return [
                    'url' => $result['secure_url'] ?? $result['url'] ?? null,
                    'path' => $result['secure_url'] ?? $result['url'] ?? null,
                    'public_id' => $result['public_id'] ?? null,
                ];
            }
            return null;
        }, $files)));
    }
}
