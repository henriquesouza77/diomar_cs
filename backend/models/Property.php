<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Property
{
    public const TABLE = 'properties';

    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT p.*, 
                    a.zipcode, a.street, a.number, a.complement, a.neighborhood,
                    a.city, a.state, a.latitude, a.longitude,
                    pt.name AS property_type_name, pt.slug AS property_type_slug,
                    c.name AS category_name, c.slug AS category_slug,
                    u.name AS agent_name, u.email AS agent_email,
                    u.phone AS agent_phone, u.whatsapp AS agent_whatsapp, u.avatar AS agent_avatar,
                    ag.creci AS agent_creci
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             LEFT JOIN property_types pt ON pt.id = p.property_type_id
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN users u ON u.id = p.agent_id
             LEFT JOIN agents ag ON ag.user_id = p.agent_id
             WHERE p.id = ? AND p.deleted_at IS NULL LIMIT 1",
            [$id]
        );
    }

   public static function findWithImages(int $id): ?array
    {
        $property = self::find($id);
        if (!$property) return null;

        $images = PropertyImage::forProperty($id);
        
        // Previne o erro silencioso e o carregamento infinito no front-end
        // caso o imóvel seja salvo sem imagens.
        if (empty($images)) {
            $images = [
                [
                    'filename' => 'placeholder.svg', // ou o nome da sua imagem padrão
                    'is_primary' => 1,
                    'alt_text' => 'Sem foto'
                ]
            ];
        }

        $property['images'] = $images;
        $property['features'] = Feature::forProperty($id);
        
        return $property;
    }

    public static function create(array $data): int
    {
        return Database::insert(self::TABLE, self::sanitizeFields($data));
    }

    public static function update(int $id, array $data): int
    {
        return Database::update(self::TABLE, self::sanitizeFields($data), 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return Database::update(
            self::TABLE,
            ['deleted_at' => date('Y-m-d H:i:s'), 'published' => 0],
            'id = :id AND deleted_at IS NULL',
            ['id' => $id]
        );
    }

    public static function togglePublished(int $id): ?bool
    {
        $property = Database::fetch(
            "SELECT published FROM properties WHERE id = ? AND deleted_at IS NULL LIMIT 1",
            [$id]
        );
        if (!$property) return null;

        $published = !(bool)$property['published'];
        Database::update(self::TABLE, ['published' => (int)$published], 'id = :id', ['id' => $id]);
        return $published;
    }

    public static function adminSearch(array $filters, int $page, int $perPage, ?int $agentId): array
    {
        $where = ['p.deleted_at IS NULL'];
        $params = [];

        if ($agentId !== null) {
            $where[] = 'p.agent_id = :agent_id';
            $params['agent_id'] = $agentId;
        }
        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE :q OR p.code LIKE :q OR a.city LIKE :q OR a.neighborhood LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['purpose'])) {
            $where[] = 'p.purpose = :purpose';
            $params['purpose'] = $filters['purpose'];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $countRow = Database::fetch(
            "SELECT COUNT(*) AS c
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             {$whereSql}",
            $params
        );

        $params['limit'] = $perPage;
        $params['offset'] = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT p.*, a.city, a.neighborhood, a.state,
                    (SELECT filename FROM property_images
                     WHERE property_id = p.id
                     ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             {$whereSql}
             ORDER BY p.updated_at DESC, p.id DESC
             LIMIT :limit OFFSET :offset",
            $params
        );

        $total = (int)($countRow['c'] ?? 0);
        return [
            'items' => $items,
            'total' => $total,
            'pages' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public static function duplicate(int $id, int $userId): ?int
    {
        $original = self::find($id);
        if (!$original) return null;

        $newCode = self::generateUniqueCode($original['code']);

        Database::beginTransaction();
        try {
            $newId = self::create([
                'code'             => $newCode,
                'agent_id'         => $userId,
                'category_id'      => $original['category_id'],
                'property_type_id' => $original['property_type_id'],
                'title'            => $original['title'] . ' (cópia)',
                'slug'             => slugify($original['title'] . '-copia-' . $newCode),
                'description'      => $original['description'],
                'purpose'          => $original['purpose'],
                'status'           => 'inativo',
                'price'            => $original['price'],
                'condo_fee'        => $original['condo_fee'],
                'iptu'             => $original['iptu'],
                'area_total'       => $original['area_total'],
                'area_built'       => $original['area_built'],
                'bedrooms'         => $original['bedrooms'],
                'suites'           => $original['suites'],
                'bathrooms'        => $original['bathrooms'],
                'parking_spaces'   => $original['parking_spaces'],
                'floor'            => $original['floor'],
                'featured'         => 0,
                'published'        => 0,
            ]);

            // Endereço
            $address = Database::fetch("SELECT * FROM property_addresses WHERE property_id = ?", [$id]);
            if ($address) {
                unset($address['id'], $address['property_id']);
                $address['property_id'] = $newId;
                Database::insert('property_addresses', $address);
            }

            Database::query(
                "INSERT INTO property_features (property_id, feature_id)
                 SELECT ?, feature_id FROM property_features WHERE property_id = ?",
                [$newId, $id]
            );
            Database::query(
                "INSERT INTO property_images (property_id, filename, is_primary, sort_order, alt_text)
                 SELECT ?, filename, is_primary, sort_order, alt_text
                 FROM property_images WHERE property_id = ?",
                [$newId, $id]
            );

            Database::commit();
            return $newId;
        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }
    }

    public static function search(array $filters, int $page = 1, int $perPage = 12): array
    {
        $where = ['p.published = 1', 'p.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['purpose'])) {
            $where[] = 'p.purpose = :purpose';
            $params['purpose'] = $filters['purpose'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE :q OR p.code LIKE :q OR a.city LIKE :q OR a.neighborhood LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['city'])) {
            $where[] = 'a.city LIKE :city';
            $params['city'] = '%' . $filters['city'] . '%';
        }

        if (!empty($filters['neighborhood'])) {
            $where[] = 'a.neighborhood LIKE :neighborhood';
            $params['neighborhood'] = '%' . $filters['neighborhood'] . '%';
        }

        if (!empty($filters['type'])) {
            $where[] = 'pt.slug = :type';
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = 'p.price >= :min_price';
            $params['min_price'] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = 'p.price <= :max_price';
            $params['max_price'] = (float)$filters['max_price'];
        }

        if (!empty($filters['bedrooms'])) {
            $where[] = 'p.bedrooms >= :bedrooms';
            $params['bedrooms'] = (int)$filters['bedrooms'];
        }

        if (!empty($filters['suites'])) {
            $where[] = 'p.suites >= :suites';
            $params['suites'] = (int)$filters['suites'];
        }

        if (!empty($filters['bathrooms'])) {
            $where[] = 'p.bathrooms >= :bathrooms';
            $params['bathrooms'] = (int)$filters['bathrooms'];
        }

        if (!empty($filters['parking_spaces'])) {
            $where[] = 'p.parking_spaces >= :parking_spaces';
            $params['parking_spaces'] = (int)$filters['parking_spaces'];
        }

        if (!empty($filters['min_area'])) {
            $where[] = 'p.area_built >= :min_area';
            $params['min_area'] = (float)$filters['min_area'];
        }

        if (!empty($filters['featured'])) {
            $where[] = 'p.featured = 1';
        }

        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params['status'] = $filters['status'];
        }

        $order = match ($filters['sort'] ?? 'recent') {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'area_desc'  => 'p.area_built DESC',
            default      => 'p.featured DESC, p.created_at DESC',
        };

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        // Conta total
        $countRow = Database::fetch(
            "SELECT COUNT(*) AS c
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             LEFT JOIN property_types pt ON pt.id = p.property_type_id
             {$whereSql}",
            $params
        );
        $total = (int)($countRow['c'] ?? 0);

        // Busca paginada
        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $items = Database::fetchAll(
            "SELECT p.*,
                    a.city, a.neighborhood, a.state,
                    pt.name AS property_type_name,
                    (SELECT filename FROM property_images
                     WHERE property_id = p.id
                     ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             LEFT JOIN property_types pt ON pt.id = p.property_type_id
             {$whereSql}
             ORDER BY {$order}
             LIMIT :limit OFFSET :offset",
            $params
        );

        return [
            'items' => $items,
            'total' => $total,
            'pages' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public static function featured(int $limit = 3): array
    {
        return Database::fetchAll(
            "SELECT p.*,
                    a.city, a.neighborhood,
                    (SELECT filename FROM property_images
                     WHERE property_id = p.id
                     ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             WHERE p.published = 1 AND p.featured = 1 AND p.deleted_at IS NULL
             ORDER BY p.created_at DESC
             LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public static function similar(int $propertyId, int $limit = 3): array
    {
        $property = self::find($propertyId);
        if (!$property) return [];

        return Database::fetchAll(
            "SELECT p.*,
                    a.city, a.neighborhood,
                    (SELECT filename FROM property_images
                     WHERE property_id = p.id
                     ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             WHERE p.id != :id
               AND p.published = 1
               AND p.deleted_at IS NULL
               AND (p.property_type_id = :type OR p.purpose = :purpose)
             ORDER BY p.featured DESC, p.created_at DESC
             LIMIT :limit",
            [
                'id'      => $propertyId,
                'type'    => $property['property_type_id'],
                'purpose' => $property['purpose'],
                'limit'   => $limit,
            ]
        );
    }

    public static function toggleFeatured(int $id): bool
    {
        $p = Database::fetch("SELECT featured FROM properties WHERE id = ? LIMIT 1", [$id]);
        if (!$p) return false;
        $newValue = $p['featured'] ? 0 : 1;
        Database::update(self::TABLE, ['featured' => $newValue], 'id = :id', ['id' => $id]);
        return (bool)$newValue;
    }

    private static function sanitizeFields(array $data): array
    {
        $allowed = [
            'code', 'agent_id', 'category_id', 'property_type_id',
            'title', 'slug', 'description', 'purpose', 'status',
            'price', 'condo_fee', 'iptu',
            'area_total', 'area_built', 'bedrooms', 'suites', 'bathrooms',
            'parking_spaces', 'floor',             'featured', 'published', 'deleted_at',
        ];

        return array_intersect_key($data, array_flip($allowed));
    }

    private static function generateUniqueCode(string $base): string
    {
        $suffix = 1;
        $newCode = $base . '-C' . $suffix;
        while (Database::fetch("SELECT id FROM properties WHERE code = ? LIMIT 1", [$newCode])) {
            $suffix++;
            $newCode = $base . '-C' . $suffix;
        }
        return $newCode;
    }

    public static function saveAddress(int $propertyId, array $data): void
    {
        $existing = Database::fetch("SELECT id FROM property_addresses WHERE property_id = ? LIMIT 1", [$propertyId]);

        $fields = [
            'zipcode'      => $data['zipcode'] ?? null,
            'street'       => $data['street'] ?? null,
            'number'       => $data['number'] ?? null,
            'complement'   => $data['complement'] ?? null,
            'neighborhood' => $data['neighborhood'] ?? null,
            'city'         => $data['city'] ?? '',
            'state'        => $data['state'] ?? '',
            'latitude'     => !empty($data['latitude']) ? (float)$data['latitude'] : null,
            'longitude'    => !empty($data['longitude']) ? (float)$data['longitude'] : null,
        ];

        if ($existing) {
            Database::update('property_addresses', $fields, 'property_id = :pid', ['pid' => $propertyId]);
        } else {
            $fields['property_id'] = $propertyId;
            Database::insert('property_addresses', $fields);
        }
    }

    public static function saveFeatures(int $propertyId, array $featureIds): void
    {
        Database::delete('property_features', 'property_id = ?', [$propertyId]);
        foreach ($featureIds as $fid) {
            $fid = (int)$fid;
            if ($fid > 0) {
                try {
                    Database::insert('property_features', [
                        'property_id' => $propertyId,
                        'feature_id'  => $fid,
                    ]);
                } catch (\Throwable $e) {
                    // duplicata, ignora
                }
            }
        }
    }

    public static function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = slugify($title);
        $slug = $base;
        $i = 1;
        while (true) {
            $sql = "SELECT id FROM properties WHERE slug = ?";
            $params = [$slug];
            if ($ignoreId) {
                $sql .= " AND id != ?";
                $params[] = $ignoreId;
            }
            $sql .= " LIMIT 1";

            if (!Database::fetch($sql, $params)) return $slug;
            $slug = $base . '-' . (++$i);
        }
    }
}