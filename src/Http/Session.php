<?php

declare(strict_types=1);

namespace App\Http;

use LogicException;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');

        session_set_cookie_params([
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function assertStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new LogicException('A session must be started first.');
        }
    }

    private static function isHttps(): bool
    {
        $https = $_SERVER['HTTPS'] ?? '';

        return $https !== '' && strtolower($https) !== 'off';
    }
}
