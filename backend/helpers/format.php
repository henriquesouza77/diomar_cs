<?php
/**
 * Formatação para exibição.
 */

if (!function_exists('format_money')) {
    function format_money($value): string {
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }
}

if (!function_exists('format_money_short')) {
    function format_money_short($value): string {
        return 'R$ ' . number_format((float)$value, 0, ',', '.');
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date, string $format = 'd/m/Y'): string {
        if (!$date) return '';
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime(?string $dt): string {
        if (!$dt) return '';
        return date('d/m/Y H:i', strtotime($dt));
    }
}

if (!function_exists('format_time_ago')) {
    function format_time_ago(?string $dt): string {
        if (!$dt) return '';
        $diff = time() - strtotime($dt);
        if ($diff < 60)    return 'agora';
        if ($diff < 3600)  return floor($diff / 60) . ' min';
        if ($diff < 86400) return floor($diff / 3600) . 'h';
        if ($diff < 604800) return floor($diff / 86400) . 'd';
        return format_date($dt);
    }
}

if (!function_exists('format_phone')) {
    function format_phone(?string $phone): string {
        $p = preg_replace('/\D/', '', (string)$phone);
        if (strlen($p) === 11) {
            return '(' . substr($p, 0, 2) . ') ' . substr($p, 2, 5) . '-' . substr($p, 7);
        }
        if (strlen($p) === 10) {
            return '(' . substr($p, 0, 2) . ') ' . substr($p, 2, 4) . '-' . substr($p, 6);
        }
        return $phone ?? '';
    }
}