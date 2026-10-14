SET @migration_sql = IF(
  (SELECT COUNT(*)
   FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'properties'
     AND COLUMN_NAME = 'deleted_at') = 0,
  'ALTER TABLE `properties` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `published`',
  'SET @migration_noop = 1'
);
PREPARE property_soft_delete_stmt FROM @migration_sql;
EXECUTE property_soft_delete_stmt;
DEALLOCATE PREPARE property_soft_delete_stmt;

SET @migration_sql = IF(
  (SELECT COUNT(*)
   FROM INFORMATION_SCHEMA.STATISTICS
   WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'properties'
     AND INDEX_NAME = 'idx_properties_deleted_at') = 0,
  'ALTER TABLE `properties` ADD KEY `idx_properties_deleted_at` (`deleted_at`)',
  'SET @migration_noop = 1'
);
PREPARE property_soft_delete_stmt FROM @migration_sql;
EXECUTE property_soft_delete_stmt;
DEALLOCATE PREPARE property_soft_delete_stmt;
