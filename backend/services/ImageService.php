<?php
declare(strict_types=1);

namespace Services;

use Core\Response;
use Middleware\AuthMiddleware;
use Models\Property;
use Models\PropertyImage;

class ImageService
{
    /** Só corretor/admin dono do imóvel mexe nas fotos dele. */
    private static function authorize(int $propertyId): void
    {
        AuthMiddleware::requireAgent();

        $property = Property::find($propertyId);
        if (!$property) Response::error('Imóvel não encontrado.', 404);

        PropertyService::assertCanManage($property);
    }

    public static function upload(int $propertyId, array $files): array
    {
        self::authorize($propertyId);

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
        $path = $config['upload_path'] . 'properties/' . basename($filename);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public static function remove(int $imageId): void
    {
        AuthMiddleware::requireAgent();   // antes de qualquer consulta: visitante não descobre quais ids existem

        $image = PropertyImage::find($imageId);
        if (!$image) Response::error('Imagem não encontrada.', 404);

        self::authorize((int) $image['property_id']);

        PropertyImage::delete($imageId);
        self::deleteFile($image['filename']);
    }

    public static function setPrimary(int $propertyId, int $imageId): void
    {
        self::authorize($propertyId);

        $image = PropertyImage::find($imageId);
        if (!$image || (int) $image['property_id'] !== $propertyId) {
            Response::error('Imagem não pertence a este imóvel.', 422);
        }

        PropertyImage::setPrimary($propertyId, $imageId);
    }

    public static function reorder(array $imageIds): void
    {
        AuthMiddleware::requireAgent();

        // Todas as imagens precisam existir e ser de imóveis que o usuário pode gerenciar
        $valid = [];
        $checked = [];
        foreach ($imageIds as $id) {
            $image = PropertyImage::find((int) $id);
            if (!$image) Response::error('Imagem não encontrada.', 404);

            $propertyId = (int) $image['property_id'];
            if (!isset($checked[$propertyId])) {
                self::authorize($propertyId);
                $checked[$propertyId] = true;
            }
            $valid[] = (int) $id;
        }

        PropertyImage::reorder($valid);
    }
}
