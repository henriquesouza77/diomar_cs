<?php
/**
 * API unificada DIOMAR_CS — todas as rotas em 1 arquivo
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

// ============================================================
// CORS + Preflight
// ============================================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$route  = (string) ($_GET['route'] ?? '');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (!$route) {
    json_error('Nenhuma rota especificada.', 400);
}

validate_csrf();

try {
    route($method, $route);
} catch (Throwable $e) {
    error_log('[API] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (APP_DEBUG) {
        json_error($e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine(), 500);
    }
    json_error('Erro interno do servidor.', 500);
}

function route(string $method, string $route): void
{
    $key = $method . ' ' . $route;

    switch ($key) {
        // AUTH
        case 'POST auth/login':    auth_login();    break;
        case 'POST auth/register': auth_register(); break;
        case 'POST auth/logout':   auth_logout();   break;
        case 'GET auth/me':        auth_me();       break;

        // PROPERTIES
        case 'GET properties':                  prop_list();           break;
        case 'GET property':                    prop_show();           break;
        case 'GET properties/similar':          prop_similar();        break;
        case 'POST properties/store':           prop_store();          break;
        case 'POST properties/update':          prop_update();         break;
        case 'POST properties/delete':          prop_delete();         break;
                case 'POST properties/duplicate':       prop_duplicate();      break;
        case 'POST properties/toggle-featured': prop_toggle_featured(); break;
        case 'POST properties/bulk-action':     prop_bulk_action();    break;
        case 'POST properties/mark-sold':       prop_mark_sold();      break;
        case 'GET properties/commissions':      prop_commissions();    break;
        case 'POST properties/restore':         prop_restore();        break;
        
        // IMAGES
        case 'POST images/upload':      img_upload();      break;
        case 'POST images/remove':      img_remove();      break;
        case 'POST images/set-primary': img_set_primary(); break;
        case 'POST images/reorder':     img_reorder();     break;

        // LEADS
        case 'POST leads/store':         lead_store();         break;
        case 'GET leads':                lead_list();          break;
        case 'POST leads/update-status': lead_update_status(); break;

        // FAVORITES
        case 'GET favorites':         fav_list();   break;
        case 'POST favorites/toggle': fav_toggle(); break;

        // DASHBOARD
        case 'GET dashboard/stats':             dash_stats();  break;
        case 'GET dashboard/recent-properties': dash_recent(); break;
        case 'GET dashboard/charts':            dash_charts(); break;

        // USERS
        case 'GET users':         user_list();   break;
        case 'GET agents':        agent_list();  break;
        case 'POST users/store':  user_store();  break;
        case 'POST users/update': user_update(); break;
        case 'POST users/delete': user_delete(); break;

        // SETTINGS
        case 'GET settings':       settings_get();  break;
        case 'POST settings/save': settings_save(); break;

        // CATÁLOGOS
        case 'GET categories':     catalogs_categories(); break;
        case 'GET property-types': catalogs_types();      break;
        case 'GET features':       catalogs_features();   break;

        default:
            json_error("Rota não encontrada: {$key}", 404);
    }
}

// ═══════════════════════════════════════════════════════════════
// AUTH
// ═══════════════════════════════════════════════════════════════
function auth_login(): void
{
    $email    = clean_email(input_str('email'));
    $password = input_str('password');

    if (!$email || !$password) {
        json_error('E-mail e senha são obrigatórios.', 422);
    }

    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_error('E-mail ou senha incorretos.', 401);
    }

    if ($user['status'] !== 'active') {
        json_error('Sua conta está inativa.', 403);
    }

    session_regenerate_id(true);
    $_SESSION['user_id']    = (int) $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];

    db()->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

    json_ok([
        'user' => [
            'id'    => (int) $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ],
    ], 'Login efetuado com sucesso.');
}

function auth_register(): void
{
    $name     = input_str('name');
    $email    = clean_email(input_str('email'));
    $password = input_str('password');
    $confirm  = input_str('password_confirm');
    $phone    = clean_phone(input_str('phone'));

    $errors = [];
    if (!$name)                 $errors['name']     = 'Nome é obrigatório.';
    if (!$email)                $errors['email']    = 'E-mail inválido.';
    if (strlen($password) < 8)  $errors['password'] = 'Mínimo 8 caracteres.';
    if ($password !== $confirm) $errors['password_confirm'] = 'Senhas não coincidem.';

    if ($errors) json_error('Erro de validação.', 422, ['fields' => $errors]);

    $check = db()->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->execute([$email]);
    if ($check->fetch()) {
        json_error('Este e-mail já está cadastrado.', 422);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = db()->prepare(
        "INSERT INTO users (role, name, email, password_hash, phone, status)
         VALUES ('client', ?, ?, ?, ?, 'active')"
    );
    $stmt->execute([$name, $email, $hash, $phone]);

    json_ok(['user_id' => (int) db()->lastInsertId()], 'Conta criada com sucesso.');
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    json_ok(null, 'Logout efetuado.');
}

function auth_me(): void
{
    json_ok(auth_user());
}

// ═══════════════════════════════════════════════════════════════
// PROPERTIES
// ═══════════════════════════════════════════════════════════════
function prop_list(): void
{
    $pdo = db();

    $where  = ['p.published = 1', 'p.deleted_at IS NULL'];
    $params = [];

    if ($v = input_str('purpose')) {
        $where[] = 'p.purpose = ?';
        $params[] = $v;
    }
    if ($v = input_str('q')) {
        $where[] = '(p.title LIKE ? OR p.code LIKE ? OR p.city LIKE ? OR p.neighborhood LIKE ?)';
        $like = "%{$v}%";
        array_push($params, $like, $like, $like, $like);
    }
    if ($v = input_str('city')) {
        $where[] = 'p.city LIKE ?';
        $params[] = "%{$v}%";
    }
    if ($v = input_str('neighborhood')) {
        $where[] = 'p.neighborhood LIKE ?';
        $params[] = "%{$v}%";
    }
    if ($v = input_str('type')) {
        $where[] = 'pt.slug = ?';
        $params[] = $v;
    }
    if ($v = input_str('min_price')) {
        $where[] = 'p.price >= ?';
        $params[] = (float) $v;
    }
    if ($v = input_str('max_price')) {
        $where[] = 'p.price <= ?';
        $params[] = (float) $v;
    }
    if ($v = input_str('bedrooms')) {
        $where[] = 'p.bedrooms >= ?';
        $params[] = (int) $v;
    }
    if ($v = input_str('suites')) {
        $where[] = 'p.suites >= ?';
        $params[] = (int) $v;
    }
    if ($v = input_str('bathrooms')) {
        $where[] = 'p.bathrooms >= ?';
        $params[] = (int) $v;
    }
    if ($v = input_str('parking_spaces')) {
        $where[] = 'p.parking_spaces >= ?';
        $params[] = (int) $v;
    }
    if ($v = input_str('min_area')) {
        $where[] = 'p.area_built >= ?';
        $params[] = (float) $v;
    }
    if (input_str('featured') === '1') {
        $where[] = 'p.featured = 1';
    }
    if ($v = input_str('status')) {
        $where[] = 'p.status = ?';
        $params[] = $v;
    }

    $sort = match (input_str('sort', 'recent')) {
        'price_asc'  => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'area_desc'  => 'p.area_built DESC',
        default      => 'p.featured DESC, p.created_at DESC',
    };

    $whereSql = implode(' AND ', $where);
    $page    = max(1, input_int('page', 1));
    $perPage = 12;
    $offset  = ($page - 1) * $perPage;

    $countSql = "SELECT COUNT(*) FROM properties p
                 LEFT JOIN property_types pt ON pt.id = p.property_type_id
                 WHERE {$whereSql}";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $sql = "SELECT p.*,
                   pt.name AS property_type_name,
                   pt.slug AS property_type_slug,
                   c.name  AS category_name,
                   (SELECT filename FROM property_images
                    WHERE property_id = p.id
                    ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
            FROM properties p
            LEFT JOIN property_types pt ON pt.id = p.property_type_id
            LEFT JOIN categories c     ON c.id  = p.category_id
            WHERE {$whereSql}
            ORDER BY {$sort}
            LIMIT {$perPage} OFFSET {$offset}";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    json_ok([
        'items' => $stmt->fetchAll(),
        'total' => $total,
        'pages' => max(1, (int) ceil($total / $perPage)),
        'page'  => $page,
    ]);
}

function prop_show(): void
{
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();

    $stmt = $pdo->prepare(
        "SELECT p.*,
                pt.name AS property_type_name, pt.slug AS property_type_slug,
                c.name  AS category_name, c.slug AS category_slug,
                u.name  AS agent_name, u.email AS agent_email,
                u.phone AS agent_phone, u.whatsapp AS agent_whatsapp,
                u.avatar AS agent_avatar, u.creci AS agent_creci
         FROM properties p
         LEFT JOIN property_types pt ON pt.id = p.property_type_id
         LEFT JOIN categories c      ON c.id  = p.category_id
         LEFT JOIN users u           ON u.id  = p.agent_id
         WHERE p.id = ? LIMIT 1"
    );
    $stmt->execute([$id]);
    $prop = $stmt->fetch();

    if (!$prop) json_error('Imóvel não encontrado.', 404);

    $stmt = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$id]);
    $prop['images'] = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        "SELECT f.id, f.name, f.slug FROM features f
         JOIN property_features pf ON pf.feature_id = f.id
         WHERE pf.property_id = ? ORDER BY f.sort_order"
    );
    $stmt->execute([$id]);
    $prop['features'] = $stmt->fetchAll();

    $prop['agent'] = $prop['agent_id'] ? [
        'id'       => (int) $prop['agent_id'],
        'name'     => $prop['agent_name'],
        'email'    => $prop['agent_email'],
        'phone'    => $prop['agent_phone'],
        'whatsapp' => $prop['agent_whatsapp'],
        'avatar'   => $prop['agent_avatar'],
        'creci'    => $prop['agent_creci'],
    ] : null;

    $pdo->prepare("UPDATE properties SET views = views + 1 WHERE id = ?")->execute([$id]);

    json_ok($prop);
}

function prop_similar(): void
{
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT property_type_id, purpose FROM properties WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $prop = $stmt->fetch();
    if (!$prop) json_ok(['items' => []]);

    $stmt = $pdo->prepare(
        "SELECT p.*,
                (SELECT filename FROM property_images
                 WHERE property_id = p.id
                 ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
         FROM properties p
         WHERE p.id != ? AND p.published = 1
           AND (p.property_type_id = ? OR p.purpose = ?)
         ORDER BY p.featured DESC, RAND() LIMIT 3"
    );
    $stmt->execute([$id, $prop['property_type_id'], $prop['purpose']]);

    json_ok(['items' => $stmt->fetchAll()]);
}

function prop_store(): void
{
    require_agent();
    $pdo = db();
    $data = build_property_data();

    if (!$data['title']) json_error('Título é obrigatório.', 422);

    // Código sequencial automático
    $data['code'] = (string) generate_property_code();

    // Slug único a partir do título
    $data['slug'] = generate_unique_slug($data['title']);

    $data['agent_id']  = auth_id();
    $data['published'] = isset($data['published']) ? $data['published'] : 1;

    $cols = implode(', ', array_keys($data));
    $ph   = implode(', ', array_fill(0, count($data), '?'));

    $stmt = $pdo->prepare("INSERT INTO properties ({$cols}) VALUES ({$ph})");
    $stmt->execute(array_values($data));
    $id = (int) $pdo->lastInsertId();

    if (isset(input()['features'])) {
        save_property_features($id, (array) input()['features']);
    }

    json_ok(['id' => $id, 'code' => $data['code']], 'Imóvel cadastrado.');
}

function prop_update(): void
{
    require_agent();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) json_error('Imóvel não encontrado.', 404);

    $u = auth_user();
    if ($u['role'] === 'agent' && (int) $existing['agent_id'] !== $u['id']) {
        json_error('Você não pode editar este imóvel.', 403);
    }

    $data = build_property_data(true);

    // Se o título mudou, gera novo slug
    if (isset($data['title']) && $data['title'] !== $existing['title']) {
        $data['slug'] = generate_unique_slug($data['title'], $id);
    }

    if ($data) {
        $set = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $vals = array_values($data);
        $vals[] = $id;
        $pdo->prepare("UPDATE properties SET {$set} WHERE id = ?")->execute($vals);
    }

    if (isset(input()['features'])) {
        save_property_features($id, (array) input()['features']);
    }

    json_ok(['id' => $id], 'Imóvel atualizado.');
}

function prop_delete(): void
{
    require_agent();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND deleted_at IS NULL LIMIT 1");
    $stmt->execute([$id]);
    $prop = $stmt->fetch();
    if (!$prop) json_error('Imóvel não encontrado.', 404);

    $u = auth_user();
    if ($u['role'] === 'agent' && (int) $prop['agent_id'] !== $u['id']) {
        json_error('Sem permissão.', 403);
    }

    // Soft delete: move para a lixeira
    $pdo->prepare("UPDATE properties SET deleted_at = NOW() WHERE id = ?")->execute([$id]);

    json_ok(null, 'Imóvel movido para a lixeira.');
}

function prop_duplicate(): void
{
    require_agent();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND deleted_at IS NULL LIMIT 1");
    $stmt->execute([$id]);
    $prop = $stmt->fetch();
    if (!$prop) json_error('Imóvel não encontrado.', 404);

    unset($prop['id'], $prop['created_at'], $prop['updated_at'], $prop['deleted_at']);

    $prop['code']  = (string) generate_property_code();
    $prop['title'] = $prop['title'] . ' (cópia)';
    $prop['slug']  = generate_unique_slug($prop['title']);

    // Reset de venda
    $prop['sold_at']          = null;
    $prop['sold_price']       = null;
    $prop['commission_value'] = null;

    // Rascunho por padrão
    $prop['status']    = 'inativo';
    $prop['featured']  = 0;
    $prop['published'] = 0;
    $prop['views']     = 0;

    $cols = implode(', ', array_keys($prop));
    $ph   = implode(', ', array_fill(0, count($prop), '?'));

    $stmt = $pdo->prepare("INSERT INTO properties ({$cols}) VALUES ({$ph})");
    $stmt->execute(array_values($prop));
    $newId = (int) $pdo->lastInsertId();

    json_ok([
        'id'           => $newId,
        'code'         => $prop['code'],
        'slug'         => $prop['slug'],
        'redirect_url' => '/diomar-cs/frontend/pages/admin/imovel-form.php?id=' . $newId,
    ], 'Imóvel duplicado.');
}

function prop_toggle_featured(): void
{
    require_agent();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT featured FROM properties WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) json_error('Imóvel não encontrado.', 404);

    $new = $row['featured'] ? 0 : 1;
    $pdo->prepare("UPDATE properties SET featured = ? WHERE id = ?")->execute([$new, $id]);

    json_ok(['featured' => (bool) $new]);
}

// ═══════════════════════════════════════════════════════════════
// HELPERS DE PROPERTY
// ═══════════════════════════════════════════════════════════════
function build_property_data(bool $partial = false): array
{
    $fields = [
        'code' => 'str', 'title' => 'str', 'description' => 'str',
        'purpose' => 'str', 'status' => 'str',
        'price' => 'money', 'condo_fee' => 'money', 'iptu' => 'money',
        'area_total' => 'float', 'area_built' => 'float',
        'bedrooms' => 'int', 'suites' => 'int', 'bathrooms' => 'int',
        'parking_spaces' => 'int', 'floor' => 'int',
        'zipcode' => 'str', 'street' => 'str', 'number' => 'str',
        'complement' => 'str', 'neighborhood' => 'str', 'city' => 'str', 'state' => 'str',
        'latitude' => 'float', 'longitude' => 'float',
        'category_id' => 'int', 'property_type_id' => 'int', 'featured' => 'bool',
    ];

    $d = [];
    foreach ($fields as $field => $type) {
        if ($partial && !array_key_exists($field, input())) continue;
        $value = input()[$field] ?? null;
        if ($value === null || $value === '') {
            if ($partial) continue;
        }
        $d[$field] = match ($type) {
            'int'   => (int) $value,
            'float' => (float) $value,
            'money' => clean_money($value),
            'bool'  => $value ? 1 : 0,
            default => trim((string) $value),
        };
    }
    return $d;
}

function save_property_features(int $propertyId, array $featureIds): void
{
    $pdo = db();
    $pdo->prepare("DELETE FROM property_features WHERE property_id = ?")->execute([$propertyId]);
    if (!$featureIds) return;

    $stmt = $pdo->prepare("INSERT IGNORE INTO property_features (property_id, feature_id) VALUES (?, ?)");
    foreach ($featureIds as $fid) {
        $fid = (int) $fid;
        if ($fid > 0) $stmt->execute([$propertyId, $fid]);
    }
}

function unique_slug(string $title, ?int $ignoreId = null): string
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
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) return $slug;
        $slug = $base . '-' . (++$i);
    }
}

function generate_code(): string
{
    $year = date('Y');
    $stmt = db()->prepare("SELECT COUNT(*) FROM properties WHERE code LIKE ?");
    $stmt->execute(["DC-{$year}-%"]);
    $n = (int) $stmt->fetchColumn() + 1;
    return sprintf('DC-%s-%04d', $year, $n);
}

// ═══════════════════════════════════════════════════════════════
// IMAGES
// ═══════════════════════════════════════════════════════════════
function img_upload(): void
{
    require_agent();
    $propertyId = input_int('property_id');
    if (!$propertyId) json_error('ID do imóvel obrigatório.', 422);
    if (empty($_FILES['images'])) json_error('Nenhuma imagem enviada.', 422);

    ensure_upload_dir();
    $uploaded = [];
    $isFirst  = count_files($propertyId) === 0;

    $files = $_FILES['images'];
    $names = is_array($files['name']) ? $files['name'] : [$files['name']];

    for ($i = 0; $i < count($names); $i++) {
        $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $size    = is_array($files['size'])     ? $files['size'][$i]     : $files['size'];
        $error   = is_array($files['error'])    ? $files['error'][$i]    : $files['error'];

        if ($error !== UPLOAD_ERR_OK) continue;
        if ($size > UPLOAD_MAX_SIZE) continue;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        if (!in_array($mime, UPLOAD_ALLOWED, true)) continue;

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $filename = $propertyId . '-' . uniqid() . '.' . $ext;
        $dest     = IMAGES_PATH . '/' . $filename;

        if (move_uploaded_file($tmpName, $dest)) {
            $stmt = db()->prepare(
                "INSERT INTO property_images (property_id, filename, is_primary, sort_order)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$propertyId, $filename, ($isFirst && $i === 0) ? 1 : 0, $i]);
            $uploaded[] = ['id' => (int) db()->lastInsertId(), 'filename' => $filename];
        }
    }

    json_ok(['images' => $uploaded], count($uploaded) . ' imagem(ns) enviada(s).');
}

function img_remove(): void
{
    require_agent();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT filename FROM property_images WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();
    if ($img) {
        $path = IMAGES_PATH . '/' . $img['filename'];
        if (is_file($path)) @unlink($path);
        $pdo->prepare("DELETE FROM property_images WHERE id = ?")->execute([$id]);
    }

    json_ok(null, 'Imagem removida.');
}

function img_set_primary(): void
{
    require_agent();
    $propertyId = input_int('property_id');
    $imageId    = input_int('image_id');
    if (!$propertyId || !$imageId) json_error('Parâmetros inválidos.', 422);

    $pdo = db();
    $pdo->prepare("UPDATE property_images SET is_primary = 0 WHERE property_id = ?")->execute([$propertyId]);
    $pdo->prepare("UPDATE property_images SET is_primary = 1 WHERE id = ?")->execute([$imageId]);

    json_ok(null, 'Imagem principal definida.');
}

function img_reorder(): void
{
    require_agent();
    $ids = input()['ids'] ?? [];
    if (!is_array($ids)) json_error('Formato inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("UPDATE property_images SET sort_order = ? WHERE id = ?");
    foreach ($ids as $i => $id) {
        $stmt->execute([(int) $i, (int) $id]);
    }

    json_ok(null, 'Ordem atualizada.');
}

function count_files(int $propertyId): int
{
    $stmt = db()->prepare("SELECT COUNT(*) FROM property_images WHERE property_id = ?");
    $stmt->execute([$propertyId]);
    return (int) $stmt->fetchColumn();
}

// ═══════════════════════════════════════════════════════════════
// LEADS
// ═══════════════════════════════════════════════════════════════
function lead_store(): void
{
    $name    = input_str('name');
    $email   = clean_email(input_str('email'));
    $phone   = clean_phone(input_str('phone'));
    $message = input_str('message');

    $errors = [];
    if (!$name)  $errors['name']  = 'Nome é obrigatório.';
    if (!$email) $errors['email'] = 'E-mail inválido.';
    if ($errors) json_error('Erro de validação.', 422, ['fields' => $errors]);

    $stmt = db()->prepare(
        "INSERT INTO leads (property_id, agent_id, name, email, phone, whatsapp, message, source)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        input_int('property_id') ?: null,
        input_int('agent_id')    ?: null,
        $name, $email, $phone, $phone, $message,
        input_str('origin', 'site'),
    ]);

    json_ok(['id' => (int) db()->lastInsertId()], 'Recebemos seu contato.');
}

function lead_list(): void
{
    require_agent();
    $pdo = db();

    $where = []; $params = [];
    if ($v = input_str('q')) {
        $where[] = '(l.name LIKE ? OR l.email LIKE ?)';
        array_push($params, "%{$v}%", "%{$v}%");
    }
    if ($v = input_str('status')) {
        $where[] = 'l.status = ?';
        $params[] = $v;
    }
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmt = $pdo->prepare(
        "SELECT l.*, p.title AS property_title, p.code AS property_code
         FROM leads l
         LEFT JOIN properties p ON p.id = l.property_id
         {$whereSql}
         ORDER BY l.created_at DESC LIMIT 100"
    );
    $stmt->execute($params);

    json_ok(['items' => $stmt->fetchAll()]);
}

function lead_update_status(): void
{
    require_agent();
    $id     = input_int('id');
    $status = input_str('status');

    $allowed = ['novo', 'em_contato', 'em_negociacao', 'convertido', 'perdido'];
    if (!in_array($status, $allowed, true)) json_error('Status inválido.', 422);

    db()->prepare("UPDATE leads SET status = ? WHERE id = ?")->execute([$status, $id]);

    json_ok(null, 'Status atualizado.');
}

// ═══════════════════════════════════════════════════════════════
// FAVORITES
// ═══════════════════════════════════════════════════════════════
function fav_list(): void
{
    $u = auth_user();
    if (!$u) json_ok([]);

    $stmt = db()->prepare(
        "SELECT f.property_id, f.created_at,
                p.title, p.code, p.price, p.purpose, p.status,
                p.bedrooms, p.bathrooms, p.parking_spaces, p.area_built,
                p.city, p.neighborhood,
                (SELECT filename FROM property_images
                 WHERE property_id = p.id
                 ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
         FROM favorites f
         JOIN properties p ON p.id = f.property_id
         WHERE f.user_id = ?
         ORDER BY f.created_at DESC"
    );
    $stmt->execute([$u['id']]);

    $items = array_map(fn($i) => [
        'property_id' => (int) $i['property_id'],
        'property'    => $i,
    ], $stmt->fetchAll());

    json_ok($items);
}

function fav_toggle(): void
{
    $u = require_login();
    $propertyId = input_int('property_id');
    if (!$propertyId) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND property_id = ? LIMIT 1");
    $stmt->execute([$u['id'], $propertyId]);

    if ($stmt->fetch()) {
        $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?")->execute([$u['id'], $propertyId]);
        $favorited = false;
    } else {
        $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)")->execute([$u['id'], $propertyId]);
        $favorited = true;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
    $stmt->execute([$u['id']]);

    json_ok(['favorited' => $favorited, 'total' => (int) $stmt->fetchColumn()]);
}

// ═══════════════════════════════════════════════════════════════
// DASHBOARD
// ═══════════════════════════════════════════════════════════════
function dash_stats(): void
{
    require_agent();
    $pdo = db();

    json_ok([
        'total_properties' => (int) $pdo->query("SELECT COUNT(*) FROM properties WHERE deleted_at IS NULL")->fetchColumn(),
        'available'        => (int) $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'disponivel' AND deleted_at IS NULL")->fetchColumn(),
        'sold'             => (int) $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'vendido' AND deleted_at IS NULL")->fetchColumn(),
        'rented'           => (int) $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'alugado' AND deleted_at IS NULL")->fetchColumn(),
        'featured'         => (int) $pdo->query("SELECT COUNT(*) FROM properties WHERE featured = 1 AND deleted_at IS NULL")->fetchColumn(),
        'total_commission' => (float) ($pdo->query("SELECT COALESCE(SUM(commission_value), 0) FROM properties WHERE status = 'vendido' AND deleted_at IS NULL")->fetchColumn() ?: 0),
        'sold_total_value' => (float) ($pdo->query("SELECT COALESCE(SUM(sold_price), 0) FROM properties WHERE status = 'vendido' AND deleted_at IS NULL")->fetchColumn() ?: 0),
    ]);
}

function dash_recent(): void
{
    require_agent();
    $stmt = db()->prepare(
        "SELECT p.id, p.title, p.code, p.status, p.price, p.featured,
                p.city, p.neighborhood,
                (SELECT filename FROM property_images
                 WHERE property_id = p.id
                 ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
         FROM properties p
         ORDER BY p.created_at DESC LIMIT 8"
    );
    $stmt->execute();
    json_ok(['items' => $stmt->fetchAll()]);
}

function dash_charts(): void
{
    require_agent();
    $pdo = db();

    json_ok([
        'properties_by_status' => $pdo->query("SELECT status AS label, COUNT(*) AS value FROM properties GROUP BY status")->fetchAll(),
        'properties_by_type'   => $pdo->query("SELECT pt.name AS label, COUNT(*) AS value FROM properties p JOIN property_types pt ON pt.id = p.property_type_id GROUP BY pt.id")->fetchAll(),
        'leads_by_month'       => $pdo->query("SELECT DATE_FORMAT(created_at, '%m/%Y') AS label, COUNT(*) AS value FROM leads WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY MIN(created_at)")->fetchAll(),
    ]);
}

// ═══════════════════════════════════════════════════════════════
// USERS
// ═══════════════════════════════════════════════════════════════
function user_list(): void
{
    require_admin();
    $role = input_str('role');
    $q    = input_str('q');

    $where = []; $params = [];
    if ($role) { $where[] = 'role = ?'; $params[] = $role; }
    if ($q)    { $where[] = '(name LIKE ? OR email LIKE ?)'; array_push($params, "%{$q}%", "%{$q}%"); }
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmt = db()->prepare(
        "SELECT id, role, name, email, phone, whatsapp, creci, avatar, status, last_login, created_at
         FROM users {$whereSql}
         ORDER BY created_at DESC LIMIT 200"
    );
    $stmt->execute($params);

    json_ok(['items' => $stmt->fetchAll()]);
}

function agent_list(): void
{
    $stmt = db()->prepare(
        "SELECT id, name, email, phone, whatsapp, creci, avatar
         FROM users WHERE role IN ('admin', 'agent') AND status = 'active'
         ORDER BY name"
    );
    $stmt->execute();
    json_ok($stmt->fetchAll());
}

function user_store(): void
{
    require_admin();
    $name  = input_str('name');
    $email = clean_email(input_str('email'));
    $pass  = input_str('password') ?: bin2hex(random_bytes(4));
    $role  = input_str('role', 'agent');

    if (!$name || !$email) json_error('Nome e e-mail obrigatórios.', 422);

    $check = db()->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) json_error('E-mail já cadastrado.', 422);

    $stmt = db()->prepare(
        "INSERT INTO users (role, name, email, password_hash, phone, whatsapp, creci, bio, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')"
    );
    $stmt->execute([
        in_array($role, ['admin', 'agent', 'client'], true) ? $role : 'agent',
        $name, $email,
        password_hash($pass, PASSWORD_BCRYPT),
        clean_phone(input_str('phone')),
        clean_phone(input_str('whatsapp')),
        input_str('creci') ?: null,
        input_str('bio') ?: null,
    ]);

    json_ok(['id' => (int) db()->lastInsertId(), 'password' => $pass], 'Usuário criado.');
}

function user_update(): void
{
    require_admin();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $data = [];
    if ($v = input_str('name'))     $data['name']  = $v;
    if ($v = clean_phone(input_str('phone')))    $data['phone'] = $v;
    if ($v = clean_phone(input_str('whatsapp'))) $data['whatsapp'] = $v;
    if ($v = input_str('creci'))    $data['creci'] = $v;
    if ($v = input_str('status'))   $data['status'] = $v;
    if ($v = input_str('role'))     $data['role']  = $v;
    if ($v = input_str('password')) $data['password_hash'] = password_hash($v, PASSWORD_BCRYPT);

    if (!$data) json_ok(null, 'Nada para atualizar.');

    $set = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
    $vals = array_values($data);
    $vals[] = $id;
    db()->prepare("UPDATE users SET {$set} WHERE id = ?")->execute($vals);

    json_ok(null, 'Usuário atualizado.');
}

function user_delete(): void
{
    $me = require_admin();
    $id = input_int('id');
    if ($id === $me['id']) json_error('Você não pode excluir sua própria conta.', 403);

    db()->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    json_ok(null, 'Usuário excluído.');
}

// ═══════════════════════════════════════════════════════════════
// SETTINGS
// ═══════════════════════════════════════════════════════════════
function settings_get(): void
{
    $stmt = db()->query("SELECT setting_key, setting_value FROM settings");
    $result = [];
    foreach ($stmt->fetchAll() as $r) $result[$r['setting_key']] = $r['setting_value'];
    json_ok($result);
}

function settings_save(): void
{
    require_admin();
    $data = input();
    unset($data['csrf_token'], $data['route']);

    $stmt = db()->prepare(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );

    foreach ($data as $k => $v) {
        if (is_scalar($v)) $stmt->execute([$k, (string) $v]);
    }

    json_ok(null, 'Configurações salvas.');
}

// ═══════════════════════════════════════════════════════════════
// CATÁLOGOS
// ═══════════════════════════════════════════════════════════════
function catalogs_categories(): void
{
    $stmt = db()->query("SELECT * FROM categories ORDER BY name");
    json_ok($stmt->fetchAll());
}

function catalogs_types(): void
{
    $catId = input_int('category_id');
    if ($catId) {
        $stmt = db()->prepare("SELECT * FROM property_types WHERE category_id = ? ORDER BY name");
        $stmt->execute([$catId]);
    } else {
        $stmt = db()->query("SELECT * FROM property_types ORDER BY name");
    }
    json_ok($stmt->fetchAll());
}

function catalogs_features(): void
{
    $stmt = db()->query("SELECT * FROM features ORDER BY name");
    json_ok($stmt->fetchAll());
}
// ═══════════════════════════════════════════════════════════════
// BLOCO 1 — NOVAS FUNÇÕES
// ═══════════════════════════════════════════════════════════════

function prop_mark_sold(): void
{
    require_agent();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $prop = $stmt->fetch();
    if (!$prop) json_error('Imóvel não encontrado.', 404);

    $soldPrice = input_float('sold_price') ?: (float) $prop['price'];
    $percent   = input_float('commission_percent') ?: 6.0;

    if ($soldPrice <= 0) {
        json_error('Preço de venda inválido.', 422);
    }

    $commission = calculate_commission($soldPrice, $percent);

    $pdo->prepare(
        "UPDATE properties
         SET status = 'vendido',
             sold_at = NOW(),
             sold_price = ?,
             commission_percent = ?,
             commission_value = ?
         WHERE id = ?"
    )->execute([$soldPrice, $percent, $commission, $id]);

    json_ok([
        'id'                 => $id,
        'sold_price'         => $soldPrice,
        'commission_percent' => $percent,
        'commission_value'   => $commission,
    ], 'Imóvel marcado como vendido.');
}

function prop_commissions(): void
{
    require_agent();

    $pdo = db();

    $where  = ["status = 'vendido'", 'deleted_at IS NULL'];
    $params = [];

    $from = input_str('from');
    $to   = input_str('to');

    if ($from) {
        $where[]  = 'sold_at >= ?';
        $params[] = $from . ' 00:00:00';
    }
    if ($to) {
        $where[]  = 'sold_at <= ?';
        $params[] = $to . ' 23:59:59';
    }

    $whereSql = 'WHERE ' . implode(' AND ', $where);

    $stmt = $pdo->prepare(
        "SELECT id, code, title, price, sold_price,
                commission_percent, commission_value, sold_at
         FROM properties
         {$whereSql}
         ORDER BY sold_at DESC"
    );
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    $totalSold = 0;
    $totalCommission = 0;
    foreach ($items as $i) {
        $totalSold       += (float) $i['sold_price'];
        $totalCommission += (float) $i['commission_value'];
    }

    json_ok([
        'items'            => $items,
        'total_sold'       => $totalSold,
        'total_commission' => $totalCommission,
        'count'            => count($items),
    ]);
}

function prop_bulk_action(): void
{
    require_agent();

    $ids    = input()['ids']    ?? [];
    $action = input_str('action');

    if (!is_array($ids) || empty($ids)) {
        json_error('Nenhum imóvel selecionado.', 422);
    }

    $ids = array_map('intval', $ids);
    $pdo = db();

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    switch ($action) {
        case 'publish':
            $sql = "UPDATE properties SET published = 1 WHERE id IN ({$placeholders})";
            $msg = 'Imóveis publicados.';
            break;
        case 'unpublish':
            $sql = "UPDATE properties SET published = 0 WHERE id IN ({$placeholders})";
            $msg = 'Imóveis despublicados.';
            break;
        case 'feature':
            $sql = "UPDATE properties SET featured = 1 WHERE id IN ({$placeholders})";
            $msg = 'Imóveis marcados como destaque.';
            break;
        case 'unfeature':
            $sql = "UPDATE properties SET featured = 0 WHERE id IN ({$placeholders})";
            $msg = 'Destaques removidos.';
            break;
        case 'delete':
            $sql = "UPDATE properties SET deleted_at = NOW() WHERE id IN ({$placeholders})";
            $msg = 'Imóveis movidos para a lixeira.';
            break;
        case 'activate':
            $sql = "UPDATE properties SET status = 'disponivel' WHERE id IN ({$placeholders})";
            $msg = 'Imóveis ativados.';
            break;
        case 'deactivate':
            $sql = "UPDATE properties SET status = 'inativo' WHERE id IN ({$placeholders})";
            $msg = 'Imóveis desativados.';
            break;
        default:
            json_error('Ação inválida.', 422);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $affected = $stmt->rowCount();

    json_ok(['affected' => $affected], $msg);
}

function prop_restore(): void
{
    require_agent();
    $id = input_int('id');
    if (!$id) json_error('ID inválido.', 422);

    $pdo = db();
    $pdo->prepare("UPDATE properties SET deleted_at = NULL WHERE id = ?")->execute([$id]);

    json_ok(['id' => $id], 'Imóvel restaurado.');
}