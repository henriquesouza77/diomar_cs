<?php
declare(strict_types=1);

namespace Core;

/**
 * Limite de tentativas por "assunto" (IP, e-mail...) dentro de uma janela de tempo.
 * Usado no login, no cadastro e no formulário de contato.
 * A tabela rate_limits é criada sozinha no primeiro uso.
 *
 * shortcut: o IP vem de REMOTE_ADDR; atrás de proxy/CDN todos teriam o mesmo IP,
 * então ao publicar atrás de um proxy confiável troque Request::ip() para ler X-Forwarded-For.
 */
class RateLimit
{
    private static bool $ready = false;

    private static function ensure(): void
    {
        if (self::$ready) return;

        Database::query(
            "CREATE TABLE IF NOT EXISTS rate_limits (
                id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                bucket     VARCHAR(32)  NOT NULL,
                subject    VARCHAR(190) NOT NULL,
                created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_rate_lookup (bucket, subject, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        self::$ready = true;
    }

    public static function tooMany(string $bucket, string $subject, int $max, int $windowSeconds): bool
    {
        self::ensure();
        $row = Database::fetch(
            "SELECT COUNT(*) AS c FROM rate_limits
             WHERE bucket = ? AND subject = ? AND created_at > (NOW() - INTERVAL ? SECOND)",
            [$bucket, $subject, $windowSeconds]
        );
        return (int) ($row['c'] ?? 0) >= $max;
    }

    public static function hit(string $bucket, string $subject): void
    {
        self::ensure();
        Database::query("INSERT INTO rate_limits (bucket, subject) VALUES (?, ?)", [$bucket, $subject]);

        // Limpeza barata: de vez em quando apaga o que tem mais de um dia
        if (random_int(1, 50) === 1) {
            Database::query("DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL 1 DAY)");
        }
    }

    public static function clear(string $bucket, string $subject): void
    {
        self::ensure();
        Database::query("DELETE FROM rate_limits WHERE bucket = ? AND subject = ?", [$bucket, $subject]);
    }

    /** Responde 429 e encerra se o assunto passou do limite. */
    public static function enforce(string $bucket, string $subject, int $max, int $windowSeconds): void
    {
        if (self::tooMany($bucket, $subject, $max, $windowSeconds)) {
            header('Retry-After: ' . $windowSeconds);
            Response::error('Muitas tentativas. Aguarde alguns minutos e tente de novo.', 429);
        }
    }
}
