<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Middleware\CsrfMiddleware;
use Middleware\AuthMiddleware;
use Models\Setting;

class SettingsController
{
    public function index(): void
    {
        Response::success(Setting::all());
    }

    public function save(): void
    {
        CsrfMiddleware::handle();
        AuthMiddleware::requireAdmin();

        $data = Request::body();
        Setting::setMany($data);
        Response::success(null, 'Configurações salvas.');
    }
}