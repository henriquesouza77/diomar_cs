<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Setting
{
    public static function all(): array
    {
        $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings");
        $result = [];
        foreach ($rows as $r) $result[$r['setting_key']] = $r['setting_value'];
        return $result;
    }

    public static function get(string $key, $default = null)
    {
        $row = Database::fetch("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1", [$key]);
        return $row ? $row['setting_value'] : $default;
    }

    public static function set(string $key, $value): void
    {
        $exists = Database::fetch("SELECT 1 FROM settings WHERE setting_key = ?", [$key]);
        if ($exists) {
            Database::update('settings', ['setting_value' => (string)$value], 'setting_key = :k', ['k' => $key]);
        } else {
            Database::insert('settings', ['setting_key' => $key, 'setting_value' => (string)$value]);
        }
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $k => $v) {
            self::set($k, $v);
        }
    }
}