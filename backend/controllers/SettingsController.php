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

        // Só as chaves conhecidas (nada de gravar csrf_token ou campos soltos)
        $allowed = ['site_name', 'site_email', 'site_phone', 'site_whatsapp', 'site_address', 'items_per_page'];
        $data = array_intersect_key(Request::body(), array_flip($allowed));
        Setting::setMany($data);
        Response::success(null, 'Configurações salvas.');
    }
}