<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Middleware\CsrfMiddleware;
use Services\PropertyService;
use Services\ImageService;

class PropertyController
{
    public function adminIndex(): void
    {
        $filters = [
            'q' => Request::get('q'),
            'status' => Request::get('status'),
            'purpose' => Request::get('purpose'),
            'page' => (int)Request::get('page', 1),
        ];

        Response::success(PropertyService::adminList($filters));
    }

    public function index(): void
    {
        $filters = [
            'purpose'        => Request::get('purpose'),
            'q'              => Request::get('q'),
            'city'           => Request::get('city'),
            'neighborhood'   => Request::get('neighborhood'),
            'type'           => Request::get('type'),
            'min_price'      => Request::get('min_price'),
            'max_price'      => Request::get('max_price'),
            'bedrooms'       => Request::get('bedrooms'),
            'suites'         => Request::get('suites'),
            'bathrooms'      => Request::get('bathrooms'),
            'parking_spaces' => Request::get('parking_spaces'),
            'min_area'       => Request::get('min_area'),
            'featured'       => Request::get('featured'),
            'status'         => Request::get('status'),
            'sort'           => Request::get('sort', 'recent'),
            'page'           => (int) Request::get('page', 1),
        ];

        $result = PropertyService::list(array_filter($filters, fn($v) => $v !== '' && $v !== null));
        Response::success($result);
    }

    public function show(): void
    {
        $id = (int) Request::get('id');
        Response::success(PropertyService::get($id));
    }

    public function store(): void
    {
        CsrfMiddleware::handle();
        $data = Request::body();

        $result = PropertyService::create($data);

        // Upload de imagens
        $files = Request::files('images');
        if ($files) {
            ImageService::upload($result['id'], $files);
        }

        Response::success($result, 'Imóvel cadastrado com sucesso.');
    }

    public function update(): void
    {
        CsrfMiddleware::handle();
        $id = (int) Request::input('id');
        $data = Request::body();

        PropertyService::update($id, $data);

        $files = Request::files('images');
        if ($files) {
            ImageService::upload($id, $files);
        }

        Response::success(['id' => $id], 'Imóvel atualizado.');
    }

    public function destroy(): void
    {
        CsrfMiddleware::handle();
        $id = (int) Request::input('id');
        PropertyService::delete($id);
        Response::success(null, 'Imóvel excluído.');
    }

    public function duplicate(): void
    {
        CsrfMiddleware::handle();
        $id = (int) Request::input('id');
        Response::success(PropertyService::duplicate($id), 'Imóvel duplicado.');
    }

    public function toggleFeatured(): void
    {
        CsrfMiddleware::handle();
        $id = (int) Request::input('id');
        Response::success(PropertyService::toggleFeatured($id));
    }

    public function togglePublished(): void
    {
        CsrfMiddleware::handle();
        $id = (int)Request::input('id');
        Response::success(PropertyService::togglePublished($id));
    }

    public function similar(): void
    {
        $id = (int) Request::get('id');
        Response::success(PropertyService::similar($id));
    }
}