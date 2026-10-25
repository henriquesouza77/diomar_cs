<?php
/**
 * Cria (ou redefine a senha d)o administrador. Só roda pela linha de comando.
 *
 *   php backend/tools/create-admin.php email@dominio.com "senha-com-8-ou-mais"
 *
 * No XAMPP/Windows:
 *   C:\xampp\php\php.exe backend\tools\create-admin.php email@dominio.com "senha-com-8-ou-mais"
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../config/bootstrap.php';

use Models\User;

$email    = clean_email($argv[1] ?? '');
$password = (string) ($argv[2] ?? '');

if (!$email || strlen($password) < 8) {
    fwrite(STDERR, "Uso: php backend/tools/create-admin.php email@dominio.com \"senha-com-8-ou-mais\"\n");
    exit(1);
}

$existing = User::findByEmail($email);

if ($existing) {
    User::update((int) $existing['id'], [
        'password' => $password,
        'role_id'  => ROLE_ADMIN,
        'status'   => 'active',
    ]);
    echo "Senha do administrador {$email} redefinida.\n";
} else {
    User::create([
        'role_id'  => ROLE_ADMIN,
        'name'     => 'Administrador',
        'email'    => $email,
        'password' => $password,
    ]);
    echo "Administrador {$email} criado.\n";
}
