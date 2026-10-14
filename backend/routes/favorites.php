<?php
return [
    'GET'  => [
        'favorites' => [\Controllers\FavoriteController::class, 'index'],
    ],
    'POST' => [
        'favorites/toggle' => [\Controllers\FavoriteController::class, 'toggle'],
    ],
];