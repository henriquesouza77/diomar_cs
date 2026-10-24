-- ============================================================
-- Adiciona suporte a login via Google OAuth
-- ============================================================
ALTER TABLE `users`
  ADD COLUMN `google_id` VARCHAR(255) NULL DEFAULT NULL AFTER `avatar`,
  MODIFY COLUMN `password_hash` VARCHAR(255) NULL DEFAULT NULL,
  ADD UNIQUE KEY `uk_users_google_id` (`google_id`);