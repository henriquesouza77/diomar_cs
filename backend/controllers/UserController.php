<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Middleware\CsrfMiddleware;
use Services\UserService;

class UserController
{
    public function index(): void
    {
        $filters = [
            'q'       => Request::get('q', ''),
            'role_id' => Request::get('role_id', ''),
            'page'    => (int) Request::get('page', 1),
        ];
        Response::success(UserService::list($filters));
    }

    public function store(): void
    {
        CsrfMiddleware::handle();
        $result = UserService::createAgent(Request::body());
        Response::success($result, 'Corretor criado com sucesso.');
    }

    public function update(): void
    {
        CsrfMiddleware::handle();
        $id = (int) Request::input('id');
        UserService::update($id, Request::body());
        Response::success(null, 'Usuário atualizado.');
    }

    public function delete(): void
    {
        CsrfMiddleware::handle();
        $id = (int) Request::input('id');
        UserService::delete($id);
        Response::success(null, 'Usuário excluído.');
    }
}