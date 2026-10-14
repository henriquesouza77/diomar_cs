<?php
return [
    'GET'  => [
        'settings' => [\Controllers\SettingsController::class, 'index'],
    ],
    'POST' => [
        'settings/save' => [\Controllers\SettingsController::class, 'save'],
    ],
];