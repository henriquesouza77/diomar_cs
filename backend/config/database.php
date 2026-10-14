<?php
/**
 * Configuração de conexão com MySQL/MariaDB (XAMPP).
 * Edite aqui caso altere credenciais no phpMyAdmin.
 */
return [
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'diomar_cs_imoveis',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
    ],
];