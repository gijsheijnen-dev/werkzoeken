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

        //alleen session ID's van de eigen server accepteren;
        ini_set('session.use_strict_mode', '1');

        /**
         * httponly: JavaScript kan de sessiecookie niet lezen, dus bij een XSS-lek kan de sessie niet gestolen worden.
         * samesite=Lax: de cookie gaat niet mee bij een POST vanaf een andere site (extra laag tegen CSRF).
         * secure: de cookie gaat alleen over HTTPS; lokaal draait het op HTTP, daarom alleen aan onder HTTPS.
         */
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
