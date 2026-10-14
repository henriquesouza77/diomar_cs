<?php
/**
 * Constantes globais.
 */
define('ROOT_PATH',      dirname(__DIR__, 2));
define('BACKEND_PATH',   ROOT_PATH . '/backend');
define('FRONTEND_PATH',  ROOT_PATH . '/frontend');
define('UPLOADS_PATH',   BACKEND_PATH . '/uploads');

// Roles
define('ROLE_ADMIN',  1);
define('ROLE_AGENT',  2);
define('ROLE_CLIENT', 3);

// Status de imóvel
define('PROPERTY_STATUS_AVAILABLE', 'disponivel');
define('PROPERTY_STATUS_SOLD',      'vendido');
define('PROPERTY_STATUS_RENTED',    'alugado');
define('PROPERTY_STATUS_RESERVED',  'reservado');
define('PROPERTY_STATUS_INACTIVE',  'inativo');

// Lead status IDs
define('LEAD_STATUS_NEW',        1);
define('LEAD_STATUS_CONTACT',    2);
define('LEAD_STATUS_NEGOTIATION',3);
define('LEAD_STATUS_CONVERTED',  4);
define('LEAD_STATUS_LOST',       5);