<?php
// Rotas de autenticação isoladas (opcional, para organização)
return [
    'POST' => [
        'auth/login'    => [\Controllers\AuthController::class, 'login'],
        'auth/register' => [\Controllers\AuthController::class, 'register'],
        'auth/logout'   => [\Controllers\AuthController::class, 'logout'],
    ],
    'GET' => [
        'auth/me' => [\Controllers\AuthController::class, 'me'],
    ],
];