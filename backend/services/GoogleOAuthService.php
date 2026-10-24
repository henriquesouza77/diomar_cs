<?php
declare(strict_types=1);

namespace Services;

class GoogleOAuthService
{
    private const AUTH_URL    = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL   = 'https://oauth2.googleapis.com/token';
    private const USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    private static function cfg(): array
    {
        return [
        'client_id'     => config('google.client_id'),
        'client_secret' => config('google.client_secret'),
        'redirect_uri'  => config('google.redirect_uri'),
        ];
    }

    /** Gera URL de autorização + state anti-CSRF salvo na sessão. */
    public static function getAuthUrl(): string
    {
        $cfg = self::cfg();
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        // ADICIONE ESTA LINHA DE DEBUG:
        error_log("[Google OAuth] State GERADO: " . $state . " | Sessão ID: " . session_id());

        $params = [
            'client_id'     => $cfg['client_id'],
            'redirect_uri'  => $cfg['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ];

        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /** Troca o "code" pelo access_token. */
    public static function exchangeCode(string $code): array
    {
        $cfg = self::cfg();

        $ch = curl_init(self::TOKEN_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'redirect_uri'  => $cfg['redirect_uri'],
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_TIMEOUT        => 15,
        ]);

        $body = curl_exec($ch);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $err) {
            throw new \RuntimeException('Falha ao contactar Google: ' . $err);
        }

        $data = json_decode($body, true);
        if (!isset($data['access_token'])) {
            throw new \RuntimeException('Google não retornou access_token: ' . $body);
        }
        return $data;
    }

    /** Busca o perfil do usuário (nome, email, foto). */
    public static function fetchUserInfo(string $accessToken): array
    {
        $ch = curl_init(self::USERINFO_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
        ]);

        $body = curl_exec($ch);
        curl_close($ch);

        $data = json_decode((string) $body, true);
        if (empty($data['sub']) || empty($data['email'])) {
            throw new \RuntimeException('Perfil Google inválido.');
        }
        return $data; // sub, email, name, picture, email_verified
    }

    /** Valida o "state" na volta do Google. */
    public static function validateState(?string $state): bool
    {
        $saved = $_SESSION['oauth_state'] ?? null;

        // ADICIONE ESTAS LINHAS DE DEBUG:
        error_log("[Google OAuth] State RECEBIDO: " . ($state ?? 'NULL'));
        error_log("[Google OAuth] State SALVO: " . ($saved ?? 'NULL'));
        error_log("[Google OAuth] Sessão ID no Callback: " . session_id());

        unset($_SESSION['oauth_state']);
        
        return is_string($state) && is_string($saved) && hash_equals($saved, $state);
    }
}