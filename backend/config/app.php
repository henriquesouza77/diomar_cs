<?php
/**
 * Configurações globais da aplicação.
 */
require_once __DIR__ . '/env.php';

$root = dirname(__DIR__, 2);

return [
    'name'          => 'DIOMAR_CS IMÓVEIS',
    'env'           => env('APP_DEBUG', 'false') === 'true' ? 'development' : 'production',
    'debug'         => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    'url'           => env('APP_URL', 'http://localhost/diomar-cs'),
    'session_name'  => 'diomar_session',
    'session_lifetime' => 0,

    // Uploads
    'upload_path'   => $root . '/backend/uploads/',
    'upload_url'    => BASE_PATH . '/backend/uploads/',
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


        // Google OAuth 2.0
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect_uri'  => env('GOOGLE_REDIRECT_URI', rtrim((string) env('APP_URL', 'http://localhost/diomar-cs'), '/') . '/api/auth/google/callback'),
    ],

];