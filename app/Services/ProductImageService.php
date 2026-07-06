<?php

declare(strict_types=1);

namespace App\Services;

class ProductImageService
{
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    private int $maxFileSize = 2097152; // 2MB

    public function upload(array $file): array
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'success' => true,
                'path' => null,
                'error' => null,
            ];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Image upload failed.',
            ];
        }

        if ($file['size'] > $this->maxFileSize) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Image size must be maximum 2MB.',
            ];
        }

        $originalName = (string)$file['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowedExtensions, true)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Allowed image formats are jpg, jpeg, png and webp.',
            ];
        }

        $fileName = uniqid('product_', true) . '.' . $extension;

        $uploadDirectory = __DIR__ . '/../../public/uploads/products/';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0775, true);
        }

        $destination = $uploadDirectory . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Could not save uploaded image.',
            ];
        }

        return [
            'success' => true,
            'path' => '/uploads/products/' . $fileName,
            'error' => null,
        ];
    }
}