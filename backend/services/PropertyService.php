<?php
declare(strict_types=1);

namespace Services;

use Core\Response;
use Core\Database;
use Models\Property;
use Middleware\AuthMiddleware;

class PropertyService
{
    public static function list(array $filters): array
    {
        $page    = max(1, (int)($filters['page'] ?? 1));
        $perPage = (int) config('items_per_page', 12);

        return Property::search($filters, $page, $perPage);
    }

    public static function adminList(array $filters): array
    {
        AuthMiddleware::requireAgent();

        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = (int)config('items_per_page', 12);
        $agentId = AuthMiddleware::userRole() === ROLE_AGENT
            ? AuthMiddleware::userId()
            : null;

        return Property::adminSearch($filters, $page, $perPage, $agentId);
    }

    public static function get(int $id): array
    {
        $property = Property::findWithImages($id);
        if (!$property) {
            Response::error('Imóvel não encontrado.', 404);
        }

        if (!(bool)$property['published']) {
            AuthMiddleware::requireAgent();
            self::assertCanManage($property);
        }

        // Incrementa visualizações
        try {
            Database::query("UPDATE properties SET views = views + 1 WHERE id = ?", [$id]);
        } catch (\Throwable $e) {}

        // Estrutura o corretor
        if ($property['agent_id']) {
            $property['agent'] = [
                'id'       => (int)$property['agent_id'],
                'name'     => $property['agent_name'],
                'email'    => $property['agent_email'],
                'phone'    => $property['agent_phone'],
                'whatsapp' => $property['agent_whatsapp'],
                'avatar'   => $property['agent_avatar'],
                'creci'    => $property['agent_creci'],
            ];
        } else {
            $property['agent'] = null;
        }

        return $property;
    }

    public static function create(array $data): array
    {
        AuthMiddleware::requireAgent();

        $errors = validate_required(['title', 'purpose', 'price', 'category_id', 'property_type_id'], $data);
        if ($errors) Response::error('Preencha os campos obrigatórios.', 422, ['fields' => $errors]);

        $userId = AuthMiddleware::userId();

        $payload = [
            'code'             => 'TMP-' . bin2hex(random_bytes(16)),
            'agent_id'         => $userId,
            'category_id'      => (int)$data['category_id'],
            'property_type_id' => (int)$data['property_type_id'],
            'title'            => clean_str($data['title'], 200),
            'slug'             => Property::generateSlug($data['title']),
            'description'      => clean_text($data['description'] ?? ''),
            'purpose'          => in_array($data['purpose'], ['venda', 'aluguel']) ? $data['purpose'] : 'venda',
            'status'           => in_array($data['status'] ?? 'disponivel', ['disponivel', 'reservado', 'vendido', 'alugado', 'inativo'], true)
                ? ($data['status'] ?? 'disponivel')
                : 'disponivel',
            'price'            => clean_money($data['price'] ?? 0),
            'condo_fee'        => clean_money($data['condo_fee'] ?? 0),
            'iptu'             => clean_money($data['iptu'] ?? 0),
            'area_total'       => (float)($data['area_total'] ?? 0),
            'area_built'       => (float)($data['area_built'] ?? 0),
            'bedrooms'         => (int)($data['bedrooms'] ?? 0),
            'suites'           => (int)($data['suites'] ?? 0),
            'bathrooms'        => (int)($data['bathrooms'] ?? 0),
            'parking_spaces'   => (int)($data['parking_spaces'] ?? 0),
            'floor'            => !empty($data['floor']) ? (int)$data['floor'] : null,
            'featured'         => !empty($data['featured']) ? 1 : 0,
            'published'        => array_key_exists('published', $data)
                ? (!empty($data['published']) ? 1 : 0)
                : 1,
        ];

        Database::beginTransaction();
        try {
            $propertyId = Property::create($payload);
            $code = sprintf('DC-%s-%04d', date('Y'), $propertyId);
            Property::update($propertyId, ['code' => $code]);
            Property::saveAddress($propertyId, $data);

            if (!empty($data['features']) && is_array($data['features'])) {
                Property::saveFeatures($propertyId, $data['features']);
            }
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }

        ActivityLogService::log('property.create', 'property', $propertyId, ['title' => $payload['title']]);

        return ['id' => $propertyId, 'code' => $code];
    }

    public static function update(int $id, array $data): array
    {
        AuthMiddleware::requireAgent();

        $property = Property::find($id);
        if (!$property) Response::error('Imóvel não encontrado.', 404);

        self::assertCanManage($property);

        $payload = [];
        foreach (['title', 'description', 'category_id', 'property_type_id'] as $field) {
            if (isset($data[$field])) $payload[$field] = $data[$field];
        }
        if (isset($data['purpose']) && in_array($data['purpose'], ['venda', 'aluguel'], true)) {
            $payload['purpose'] = $data['purpose'];
        }
        if (isset($data['status']) && in_array($data['status'], ['disponivel', 'reservado', 'vendido', 'alugado', 'inativo'], true)) {
            $payload['status'] = $data['status'];
        }
        if (array_key_exists('published', $data)) {
            $payload['published'] = !empty($data['published']) ? 1 : 0;
        }
        foreach (['price', 'condo_fee', 'iptu'] as $field) {
            if (isset($data[$field])) $payload[$field] = clean_money($data[$field]);
        }
        foreach (['area_total', 'area_built'] as $field) {
            if (isset($data[$field])) $payload[$field] = (float)$data[$field];
        }
        foreach (['bedrooms', 'suites', 'bathrooms', 'parking_spaces', 'floor'] as $field) {
            if (isset($data[$field])) $payload[$field] = (int)$data[$field];
        }

        if (isset($data['title'])) {
            $payload['slug'] = Property::generateSlug($data['title'], $id);
        }

        $payload['featured'] = !empty($data['featured']) ? 1 : 0;

        if ($payload) Property::update($id, $payload);
        Property::saveAddress($id, $data);

        if (isset($data['features']) && is_array($data['features'])) {
            Property::saveFeatures($id, $data['features']);
        }

        ActivityLogService::log('property.update', 'property', $id);
        return ['id' => $id];
    }

    public static function delete(int $id): void
    {
        AuthMiddleware::requireAgent();

        $property = Property::find($id);
        if (!$property) Response::error('Imóvel não encontrado.', 404);

        self::assertCanManage($property);

        Property::delete($id);
        ActivityLogService::log('property.delete', 'property', $id);
    }

    public static function duplicate(int $id): array
    {
        AuthMiddleware::requireAgent();
        $property = Property::find($id);
        if (!$property) Response::error('Imóvel não encontrado.', 404);
        self::assertCanManage($property);

        $newId = Property::duplicate($id, (int)AuthMiddleware::userId());
        if (!$newId) Response::error('Não foi possível duplicar o imóvel.', 500);

        ActivityLogService::log('property.duplicate', 'property', $newId, ['from' => $id]);
        return ['id' => $newId];
    }

    public static function toggleFeatured(int $id): array
    {
        AuthMiddleware::requireAgent();
        $property = Property::find($id);
        if (!$property) Response::error('Imóvel não encontrado.', 404);
        self::assertCanManage($property);

        $value = Property::toggleFeatured($id);
        ActivityLogService::log('property.toggle_featured', 'property', $id, ['featured' => $value]);
        return ['featured' => $value];
    }

    public static function togglePublished(int $id): array
    {
        AuthMiddleware::requireAgent();
        $property = Property::find($id);
        if (!$property) Response::error('Imóvel não encontrado.', 404);
        self::assertCanManage($property);

        $value = Property::togglePublished($id);
        if ($value === null) Response::error('Imóvel não encontrado.', 404);

        ActivityLogService::log('property.toggle_published', 'property', $id, ['published' => $value]);
        return ['published' => $value];
    }

    public static function similar(int $id): array
    {
        return ['items' => Property::similar($id, 3)];
    }

    private static function assertCanManage(array $property): void
    {
        if (AuthMiddleware::userRole() === ROLE_AGENT && (int)$property['agent_id'] !== AuthMiddleware::userId()) {
            Response::error('Você não pode gerenciar este imóvel.', 403);
        }
    }
}