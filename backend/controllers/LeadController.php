<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Middleware\CsrfMiddleware;
use Services\LeadService;

class LeadController
{
    public function store(): void
    {
        CsrfMiddleware::handle();
        $result = LeadService::create(Request::body());
        Response::success($result, 'Recebemos seu contato. Em breve retornaremos.');
    }

    public function index(): void
    {
        $filters = [
            'q'         => Request::get('q'),
            'status_id' => Request::get('status_id'),
            'page'      => (int) Request::get('page', 1),
        ];
        Response::success(LeadService::list(array_filter($filters)));
    }

    public function updateStatus(): void
    {
        CsrfMiddleware::handle();
        $id = (int) Request::input('id');
        $statusId = (int) Request::input('status_id');
        LeadService::updateStatus($id, $statusId);
        Response::success(null, 'Status atualizado.');
    }
}