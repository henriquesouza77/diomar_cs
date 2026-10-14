<?php
/**
 * Configurações globais da aplicação.
 */
$root = dirname(__DIR__, 2);

return [
    'name'          => 'DIOMAR_CS IMÓVEIS',
    'env'           => 'development',    // development | production
    'debug'         => true,
    'url'           => 'http://localhost/diomar-cs',
    'session_name'  => 'diomar_session',
    'session_lifetime' => 0,

    // Uploads
    'upload_path'   => $root . '/backend/uploads/',
    'upload_url'    => '/diomar-cs/backend/uploads/',
    'max_upload'    => 5 * 1024 * 1024, // 5MB
    'allowed_mimes' => [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ],

    // Paginação
    'items_per_page' => 12,

    // Segurança
    'password_min_length' => 8,
    'login_max_attempts'  => 5,
    'login_lockout_time'  => 900,      // 15 min

    // Timezone
    'timezone'      => 'America/Sao_Paulo',
];