<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Middleware\CsrfMiddleware;
use Services\FavoriteService;

class FavoriteController
{
    public function index(): void
    {
        Response::success(FavoriteService::list());
    }

    public function toggle(): void
    {
        CsrfMiddleware::handle();
        $propertyId = (int) Request::input('property_id');
        Response::success(FavoriteService::toggle($propertyId));
    }
}