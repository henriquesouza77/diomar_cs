<?php
declare(strict_types=1);

namespace Services;

use Core\Response;
use Models\PropertyImage;

class ImageService
{
    public static function upload(int $propertyId, array $files): array
    {
        $uploaded = [];
        $config = require BACKEND_PATH . '/config/app.php';
        $dir = $config['upload_path'] . 'properties/';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $isFirst = count(PropertyImage::forProperty($propertyId)) === 0;

        foreach ($files as $i => $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) continue;
            if ($file['size'] > $config['max_upload']) continue;

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!isset($config['allowed_mimes'][$mime])) continue;

            $ext = $config['allowed_mimes'][$mime];
            $filename = $propertyId . '-' . uniqid() . '.' . $ext;
            $dest = $dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $imageId = PropertyImage::create($propertyId, [
                    'filename'   => $filename,
                    'is_primary' => ($isFirst && $i === 0) ? 1 : 0,
                    'sort_order' => $i,
                ]);
                $uploaded[] = ['id' => $imageId, 'filename' => $filename];
            }
        }

        return $uploaded;
    }

    public static function deleteFile(string $filename): void
    {
        $config = require BACKEND_PATH . '/config/app.php';
        $path = $config['upload_path'] . 'properties/' . $filename;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public static function remove(int $imageId): void
    {
        $image = PropertyImage::delete($imageId);
        if ($image) {
            self::deleteFile($image['filename']);
        }
    }

    public static function setPrimary(int $propertyId, int $imageId): void
    {
        PropertyImage::setPrimary($propertyId, $imageId);
    }

    public static function reorder(array $imageIds): void
    {
        PropertyImage::reorder($imageIds);
    }
}