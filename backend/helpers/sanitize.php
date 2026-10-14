<?php
/**
 * Sanitização e validação básica.
 */

if (!function_exists('clean_str')) {
    function clean_str(?string $value, int $max = 255): string {
        $value = trim((string) $value);
        $value = strip_tags($value);
        return mb_substr($value, 0, $max);
    }
}

if (!function_exists('clean_text')) {
    function clean_text(?string $value, int $max = 5000): string {
        $value = trim((string) $value);
        return mb_substr($value, 0, $max);
    }
}

if (!function_exists('clean_email')) {
    function clean_email(?string $email): ?string {
        $email = filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL);
        return $email ?: null;
    }
}

if (!function_exists('clean_phone')) {
    function clean_phone(?string $phone): ?string {
        $digits = preg_replace('/\D/', '', (string) $phone);
        return $digits ?: null;
    }
}

if (!function_exists('clean_money')) {
    function clean_money($value): float {
        if (is_numeric($value)) return (float) $value;
        $value = preg_replace('/[^\d,.]/', '', (string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return (float) $value;
    }
}

if (!function_exists('sanitize_filename')) {
    function sanitize_filename(string $filename): string {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return substr($filename, 0, 200);
    }
}

if (!function_exists('validate_required')) {
    function validate_required(array $fields, array $data): array {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = 'Este campo é obrigatório.';
            }
        }
        return $errors;
    }
}