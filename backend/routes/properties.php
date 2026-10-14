<?php
return [
    'GET' => [
        'properties'         => [\Controllers\PropertyController::class, 'index'],
        'properties/admin-list' => [\Controllers\PropertyController::class, 'adminIndex'],
        'property'           => [\Controllers\PropertyController::class, 'show'],
        'properties/similar' => [\Controllers\PropertyController::class, 'similar'],
    ],
    'POST' => [
        'properties/store'           => [\Controllers\PropertyController::class, 'store'],
        'properties/update'          => [\Controllers\PropertyController::class, 'update'],
        'properties/delete'          => [\Controllers\PropertyController::class, 'destroy'],
        'properties/duplicate'       => [\Controllers\PropertyController::class, 'duplicate'],
        'properties/toggle-featured' => [\Controllers\PropertyController::class, 'toggleFeatured'],
        'properties/toggle-published' => [\Controllers\PropertyController::class, 'togglePublished'],
    ],
];