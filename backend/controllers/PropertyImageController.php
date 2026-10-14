<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Middleware\CsrfMiddleware;
use Services\ImageService;

class PropertyImageController
{
    public function upload(): void
    {
        CsrfMiddleware::handle();
        $propertyId = (int) Request::input('property_id');
        $files = Request::files('images');

        if (!$files) Response::error('Nenhuma imagem enviada.', 422);

        $uploaded = ImageService::upload($propertyId, $files);
        Response::success(['images' => $uploaded], 'Imagens enviadas.');
    }

    public function remove(): void
    {
        CsrfMiddleware::handle();
        $imageId = (int) Request::input('id');
        ImageService::remove($imageId);
        Response::success(null, 'Imagem removida.');
    }

    public function setPrimary(): void
    {
        CsrfMiddleware::handle();
        $propertyId = (int) Request::input('property_id');
        $imageId    = (int) Request::input('image_id');
        ImageService::setPrimary($propertyId, $imageId);
        Response::success(null, 'Imagem principal definida.');
    }

    public function reorder(): void
    {
        CsrfMiddleware::handle();
        $ids = Request::input('ids', []);
        ImageService::reorder((array)$ids);
        Response::success(null, 'Ordem atualizada.');
    }
}