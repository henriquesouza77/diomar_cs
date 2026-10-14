<?php
return [
    'GET' => [
        'users'  => [\Controllers\UserController::class, 'index'],
        'agents' => [\Controllers\AgentController::class, 'index'],
    ],
    'POST' => [
        'users/store'  => [\Controllers\UserController::class, 'store'],
        'users/update' => [\Controllers\UserController::class, 'update'],
        'users/delete' => [\Controllers\UserController::class, 'delete'],
    ],
];