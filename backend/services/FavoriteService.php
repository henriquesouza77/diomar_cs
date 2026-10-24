<?php
declare(strict_types=1);

namespace Services;

use Core\Response;
use Models\Favorite;
use Models\Property;
use Middleware\AuthMiddleware;

class FavoriteService
{
    public static function list(): array
    {
        AuthMiddleware::requireLogin();
        return Favorite::forUser(AuthMiddleware::userId());
    }

    public static function toggle(int $propertyId): array
    {
        AuthMiddleware::requireLogin();
        $userId = AuthMiddleware::userId();

        if ($propertyId <= 0) {
            Response::error('Imóvel inválido.', 422);
        }

        $property = Property::find($propertyId);
        if (!$property || !(bool)$property['published']) {
            Response::error('Imóvel não encontrado.', 404);
        }

        $favorited = Favorite::toggle($userId, $propertyId);
        return [
            'favorited' => $favorited,
            'total'     => Favorite::countForUser($userId),
        ];
    }
}