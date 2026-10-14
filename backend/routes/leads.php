<?php
return [
    'GET'  => [
        'leads' => [\Controllers\LeadController::class, 'index'],
    ],
    'POST' => [
        'leads/store'         => [\Controllers\LeadController::class, 'store'],
        'leads/update-status' => [\Controllers\LeadController::class, 'updateStatus'],
    ],
];