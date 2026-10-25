-- ================================================================
-- DIOMAR_CS IMÓVEIS — Banco de dados completo
-- MySQL / MariaDB — pronto para importar no phpMyAdmin
-- ================================================================
-- Uso:
--   1. Abra http://localhost/phpmyadmin
--   2. Aba "Importar" → escolha este arquivo → Executar
--   3. O banco iniciará VAZIO (sem imóveis, sem leads fictícios)
--   4. Crie o administrador: php backend/tools/create-admin.php seu@email.com "senha"
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ----------------------------------------------------------------
-- Criação do banco
-- ----------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `diomar_cs_imoveis`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `diomar_cs_imoveis`;

SET FOREIGN_KEY_CHECKS = 0;

-- ================================================================
-- 1. ROLES
-- ================================================================
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(50)  NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
  (1, 'admin',  'Administrador com acesso total ao sistema'),
  (2, 'agent',  'Corretor de imóveis'),
  (3, 'client', 'Cliente / usuário comum');

-- ================================================================
-- 2. PERMISSIONS
-- ================================================================
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`        VARCHAR(80)  NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_permissions_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `slug`, `description`) VALUES
  (1,  'properties.view',    'Visualizar imóveis'),
  (2,  'properties.create',  'Criar imóveis'),
  (3,  'properties.edit',    'Editar imóveis'),
  (4,  'properties.delete',  'Excluir imóveis'),
  (5,  'leads.view',         'Visualizar leads'),
  (6,  'leads.edit',         'Editar leads'),
  (7,  'users.view',         'Visualizar usuários'),
  (8,  'users.edit',         'Editar usuários'),
  (9,  'dashboard.view',     'Acessar dashboard'),
  (10, 'settings.edit',      'Editar configurações');

-- ================================================================
-- 3. ROLE_PERMISSIONS
-- ================================================================
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id`       INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  KEY `idx_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_role`       FOREIGN KEY (`role_id`)       REFERENCES `roles` (`id`)       ON DELETE CASCADE,
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin tem tudo
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
  (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10);

-- Corretor
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
  (2,1),(2,2),(2,3),(2,5),(2,6),(2,9);

-- Cliente
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
  (3,1);

-- ================================================================
-- 4. USERS
-- ================================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`        INT UNSIGNED NOT NULL DEFAULT 3,
  `name`           VARCHAR(150) NOT NULL,
  `email`          VARCHAR(180) NOT NULL,
  `password_hash`  VARCHAR(255) NOT NULL,
  `phone`          VARCHAR(20)  DEFAULT NULL,
  `whatsapp`       VARCHAR(20)  DEFAULT NULL,
  `avatar`         VARCHAR(255) DEFAULT NULL,
  `status`         ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
  `last_login`     DATETIME     DEFAULT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_role`    (`role_id`),
  KEY `idx_users_status`  (`status`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 5. AGENTS
-- ================================================================
DROP TABLE IF EXISTS `agents`;
CREATE TABLE `agents` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `creci`       VARCHAR(30)  DEFAULT NULL,
  `bio`         TEXT         DEFAULT NULL,
  `specialties` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_agents_user` (`user_id`),
  CONSTRAINT `fk_agents_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 6. CATEGORIES
-- ================================================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`  VARCHAR(80) NOT NULL,
  `slug`  VARCHAR(80) NOT NULL,
  `icon`  VARCHAR(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categories_name` (`name`),
  UNIQUE KEY `uk_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`) VALUES
  (1, 'Residencial', 'residencial', 'home'),
  (2, 'Comercial',   'comercial',   'building'),
  (3, 'Rural',       'rural',       'tree'),
  (4, 'Terrenos',    'terrenos',    'map');

-- ================================================================
-- 7. PROPERTY_TYPES
-- ================================================================
DROP TABLE IF EXISTS `property_types`;
CREATE TABLE `property_types` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `name`        VARCHAR(80) NOT NULL,
  `slug`        VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_property_types_slug` (`slug`),
  KEY `idx_property_types_cat` (`category_id`),
  CONSTRAINT `fk_property_types_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `property_types` (`id`, `category_id`, `name`, `slug`) VALUES
  (1,  1, 'Apartamento',    'apartamento'),
  (2,  1, 'Casa',           'casa'),
  (3,  1, 'Cobertura',      'cobertura'),
  (4,  1, 'Kitnet',         'kitnet'),
  (5,  1, 'Studio',         'studio'),
  (6,  2, 'Sala Comercial', 'sala-comercial'),
  (7,  2, 'Loja',           'loja'),
  (8,  2, 'Galpão',         'galpao'),
  (9,  2, 'Prédio Comercial','predio-comercial'),
  (10, 3, 'Sítio',          'sitio'),
  (11, 3, 'Fazenda',        'fazenda'),
  (12, 3, 'Chácara',        'chacara'),
  (13, 4, 'Terreno',        'terreno'),
  (14, 4, 'Lote',           'lote');

-- ================================================================
-- 8. FEATURES
-- ================================================================
DROP TABLE IF EXISTS `features`;
CREATE TABLE `features` (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(80) NOT NULL,
  `slug`     VARCHAR(80) NOT NULL,
  `icon`     VARCHAR(60) DEFAULT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'geral',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_features_name` (`name`),
  UNIQUE KEY `uk_features_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `features` (`id`, `name`, `slug`, `icon`, `category`) VALUES
  (1,  'Piscina',          'piscina',          'waves',       'lazer'),
  (2,  'Churrasqueira',    'churrasqueira',    'flame',       'lazer'),
  (3,  'Academia',         'academia',         'dumbbell',    'lazer'),
  (4,  'Elevador',         'elevador',         'arrow-up',    'geral'),
  (5,  'Varanda',          'varanda',          'sun',         'geral'),
  (6,  'Ar-condicionado',  'ar-condicionado',  'snowflake',   'geral'),
  (7,  'Jardim',           'jardim',           'leaf',        'externo'),
  (8,  'Portaria 24h',     'portaria-24h',     'shield',      'seguranca'),
  (9,  'Salão de festas',  'salao-festas',     'party',       'lazer'),
  (10, 'Mobiliado',        'mobiliado',        'sofa',        'geral'),
  (11, 'Playground',       'playground',       'play',        'lazer'),
  (12, 'Quadra esportiva', 'quadra-esportiva', 'activity',    'lazer'),
  (13, 'Sauna',            'sauna',            'thermometer', 'lazer'),
  (14, 'Solarium',         'solarium',         'sun',         'lazer'),
  (15, 'Home office',      'home-office',      'briefcase',   'geral'),
  (16, 'Closet',           'closet',           'box',         'geral'),
  (17, 'Lavabo',           'lavabo',           'droplet',     'geral'),
  (18, 'Copa',             'copa',             'coffee',      'geral'),
  (19, 'Despensa',         'despensa',         'package',     'geral'),
  (20, 'Quintal',          'quintal',          'home',        'externo');

-- ================================================================
-- 9. LEAD_STATUSES
-- ================================================================
DROP TABLE IF EXISTS `lead_statuses`;
CREATE TABLE `lead_statuses` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`  VARCHAR(50) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#64748B',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_lead_statuses_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lead_statuses` (`id`, `name`, `color`) VALUES
  (1, 'Novo',          '#3B82F6'),
  (2, 'Em contato',    '#F59E0B'),
  (3, 'Em negociação', '#8B5CF6'),
  (4, 'Convertido',    '#10B981'),
  (5, 'Perdido',       '#EF4444');

-- ================================================================
-- 10. PROPERTIES
-- ================================================================
DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`              VARCHAR(30)  NOT NULL,
  `agent_id`          INT UNSIGNED DEFAULT NULL,
  `category_id`       INT UNSIGNED NOT NULL,
  `property_type_id`  INT UNSIGNED NOT NULL,
  `title`             VARCHAR(200) NOT NULL,
  `slug`              VARCHAR(220) NOT NULL,
  `description`       TEXT         DEFAULT NULL,
  `purpose`           ENUM('venda','aluguel') NOT NULL,
  `status`            ENUM('disponivel','vendido','alugado','reservado','inativo') NOT NULL DEFAULT 'disponivel',
  `price`             DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `condo_fee`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `iptu`              DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `area_total`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `area_built`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `bedrooms`          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `suites`            TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `bathrooms`         TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `parking_spaces`    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `floor`             SMALLINT DEFAULT NULL,
  `featured`          TINYINT(1) NOT NULL DEFAULT 0,
  `published`         TINYINT(1) NOT NULL DEFAULT 1,
  `deleted_at`        TIMESTAMP NULL DEFAULT NULL,
  `views`             INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_properties_code` (`code`),
  UNIQUE KEY `uk_properties_slug` (`slug`),
  KEY `idx_properties_agent`     (`agent_id`),
  KEY `idx_properties_category`  (`category_id`),
  KEY `idx_properties_type`      (`property_type_id`),
  KEY `idx_properties_purpose`   (`purpose`),
  KEY `idx_properties_status`    (`status`),
  KEY `idx_properties_featured`  (`featured`),
  KEY `idx_properties_price`     (`price`),
  KEY `idx_properties_published` (`published`),
  KEY `idx_properties_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_properties_agent`    FOREIGN KEY (`agent_id`)         REFERENCES `users` (`id`)          ON DELETE SET NULL,
  CONSTRAINT `fk_properties_category` FOREIGN KEY (`category_id`)      REFERENCES `categories` (`id`)     ON DELETE RESTRICT,
  CONSTRAINT `fk_properties_type`     FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 11. PROPERTY_ADDRESSES
-- ================================================================
DROP TABLE IF EXISTS `property_addresses`;
CREATE TABLE `property_addresses` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id`  INT UNSIGNED NOT NULL,
  `zipcode`      VARCHAR(10)  DEFAULT NULL,
  `street`       VARCHAR(200) DEFAULT NULL,
  `number`       VARCHAR(20)  DEFAULT NULL,
  `complement`   VARCHAR(100) DEFAULT NULL,
  `neighborhood` VARCHAR(120) DEFAULT NULL,
  `city`         VARCHAR(120) NOT NULL,
  `state`        CHAR(2)      NOT NULL,
  `latitude`     DECIMAL(10,7) DEFAULT NULL,
  `longitude`    DECIMAL(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_property_addresses_property` (`property_id`),
  KEY `idx_property_addresses_city` (`city`),
  KEY `idx_property_addresses_neighborhood` (`neighborhood`),
  CONSTRAINT `fk_property_addresses_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 12. PROPERTY_FEATURES (pivô N:N)
-- ================================================================
DROP TABLE IF EXISTS `property_features`;
CREATE TABLE `property_features` (
  `property_id` INT UNSIGNED NOT NULL,
  `feature_id`  INT UNSIGNED NOT NULL,
  PRIMARY KEY (`property_id`, `feature_id`),
  KEY `idx_pf_feature` (`feature_id`),
  CONSTRAINT `fk_pf_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pf_feature`  FOREIGN KEY (`feature_id`)  REFERENCES `features` (`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 13. PROPERTY_IMAGES
-- ================================================================
DROP TABLE IF EXISTS `property_images`;
CREATE TABLE `property_images` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `filename`    VARCHAR(255) NOT NULL,
  `is_primary`  TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `alt_text`    VARCHAR(200) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pi_property` (`property_id`),
  KEY `idx_pi_primary`  (`is_primary`),
  CONSTRAINT `fk_pi_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 14. LEADS
-- ================================================================
DROP TABLE IF EXISTS `leads`;
CREATE TABLE `leads` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED DEFAULT NULL,
  `agent_id`    INT UNSIGNED DEFAULT NULL,
  `status_id`   INT UNSIGNED NOT NULL DEFAULT 1,
  `name`        VARCHAR(150) NOT NULL,
  `email`       VARCHAR(180) NOT NULL,
  `phone`       VARCHAR(20)  DEFAULT NULL,
  `whatsapp`    VARCHAR(20)  DEFAULT NULL,
  `message`     TEXT         DEFAULT NULL,
  `source`      VARCHAR(60)  NOT NULL DEFAULT 'site',
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_leads_property` (`property_id`),
  KEY `idx_leads_agent`    (`agent_id`),
  KEY `idx_leads_status`   (`status_id`),
  KEY `idx_leads_created`  (`created_at`),
  CONSTRAINT `fk_leads_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)     ON DELETE SET NULL,
  CONSTRAINT `fk_leads_agent`    FOREIGN KEY (`agent_id`)    REFERENCES `users` (`id`)          ON DELETE SET NULL,
  CONSTRAINT `fk_leads_status`   FOREIGN KEY (`status_id`)   REFERENCES `lead_statuses` (`id`)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 15. FAVORITES
-- ================================================================
DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `user_id`     INT UNSIGNED NOT NULL,
  `property_id` INT UNSIGNED NOT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `property_id`),
  KEY `idx_favorites_property` (`property_id`),
  CONSTRAINT `fk_favorites_user`     FOREIGN KEY (`user_id`)     REFERENCES `users` (`id`)      ON DELETE CASCADE,
  CONSTRAINT `fk_favorites_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 16. CONTACT_MESSAGES
-- ================================================================
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(180) NOT NULL,
  `phone`      VARCHAR(20)  DEFAULT NULL,
  `subject`    VARCHAR(200) DEFAULT NULL,
  `message`    TEXT         NOT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 17. SETTINGS
-- ================================================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key`   VARCHAR(80) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
  ('site_name',       'DIOMAR_CS IMÓVEIS'),
  ('site_email',      'contato@diomarcsimoveis.com.br'),
  ('site_phone',      '(11) 99999-9999'),
  ('site_whatsapp',   '5511999999999'),
  ('site_address',    'São Paulo - SP'),
  ('items_per_page',  '12');

-- ================================================================
-- 18. NOTIFICATIONS
-- ================================================================
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `title`      VARCHAR(200) NOT NULL,
  `message`    TEXT DEFAULT NULL,
  `link`       VARCHAR(255) DEFAULT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_read` (`is_read`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- 19. ACTIVITY_LOGS
-- ================================================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED DEFAULT NULL,
  `action`     VARCHAR(80) NOT NULL,
  `entity`     VARCHAR(60) DEFAULT NULL,
  `entity_id`  INT UNSIGNED DEFAULT NULL,
  `details`    TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_al_user`   (`user_id`),
  KEY `idx_al_entity` (`entity`, `entity_id`),
  KEY `idx_al_date`   (`created_at`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- O administrador NÃO vem no banco (sem senha padrão pública).
-- Crie-o depois de importar:
--   php backend/tools/create-admin.php seu@email.com "senha-com-8-ou-mais"

-- ================================================================
-- FINALIZAÇÃO
-- ================================================================
SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- FIM DO SCRIPT
-- 
-- O banco está PRONTO e VAZIO (apenas catálogos, roles, permissões,
-- status de leads, categorias, tipos, características e 1 admin).
-- 
-- Nenhum imóvel, lead, corretor ou venda fictícia foi criado.
-- Cadastre imóveis reais pelo painel administrativo.
-- ================================================================