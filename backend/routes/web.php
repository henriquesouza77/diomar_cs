<?php
declare(strict_types=1);

$routes = [];
$routeFiles = [
    'auth.php',
    'dashboard.php',
    'properties.php',
    'favorites.php',
    'leads.php',
    'users.php',
    'settings.php',
];

foreach ($routeFiles as $routeFile) {
    $group = require __DIR__ . '/' . $routeFile;
    foreach ($group as $routeMethod => $definitions) {
        $routeMethod = strtoupper($routeMethod);
        $routes[$routeMethod] = array_replace($routes[$routeMethod] ?? [], $definitions);
    }
}

$routes['GET'] = array_replace($routes['GET'] ?? [], [
    'categories' => [\Controllers\CategoryController::class, 'categories'],
    'property-types' => [\Controllers\CategoryController::class, 'propertyTypes'],
    'features' => [\Controllers\CategoryController::class, 'features'],
]);

$routes['POST'] = array_replace($routes['POST'] ?? [], [
    'property-images/upload' => [\Controllers\PropertyImageController::class, 'upload'],
    'property-images/remove' => [\Controllers\PropertyImageController::class, 'remove'],
    'property-images/set-primary' => [\Controllers\PropertyImageController::class, 'setPrimary'],
    'property-images/reorder' => [\Controllers\PropertyImageController::class, 'reorder'],
]);

return $routes;
