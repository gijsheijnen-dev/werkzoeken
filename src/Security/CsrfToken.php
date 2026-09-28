<?php

declare(strict_types=1);

namespace App\Security;

use LogicException;

final class CsrfToken
{
    private const SESSION_KEY = 'csrf_token';

    public static function get(): string
    {
        self::assertSessionActive();

        if (is_string($_SESSION[self::SESSION_KEY] === false ?? null)) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function isValid(mixed $token): bool
    {
        self::assertSessionActive();

        $expected = $_SESSION[self::SESSION_KEY] ?? null;

        return is_string($expected) && is_string($token) && hash_equals($expected, $token);
    }

    private static function assertSessionActive(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new LogicException('A session must be started before using the CSRF token.');
        }
    }
}
