<?php
return [
    'GET' => [
        'dashboard/stats'             => [\Controllers\DashboardController::class, 'stats'],
        'dashboard/recent-properties' => [\Controllers\DashboardController::class, 'recentProperties'],
        'dashboard/charts'            => [\Controllers\DashboardController::class, 'charts'],
    ],
];