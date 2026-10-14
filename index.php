<?php
/**
 * DIOMAR_CS IMÓVEIS — Entry point raiz
 * Redireciona acessos a /diomar-cs/ para a home do frontend.
 */
declare(strict_types=1);

// Detecta o base path automaticamente
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Se já está no frontend, não redireciona
if (strpos($_SERVER['REQUEST_URI'], '/frontend/') !== false) {
    require __DIR__ . '/frontend/pages/index.php';
    exit;
}

// Redireciona para a home
header('Location: ' . $base . '/frontend/pages/index.php');
exit;