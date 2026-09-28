<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\Session;

final class CsrfToken
{
    private const SESSION_KEY = 'csrf_token';

    public static function get(): string
    {
        Session::assertStarted();

        if (is_string($_SESSION[self::SESSION_KEY] ?? null) === false) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function isValid(mixed $token): bool
    {
        Session::assertStarted();

        $expected = $_SESSION[self::SESSION_KEY] ?? null;

        return is_string($expected) && is_string($token) && hash_equals($expected, $token);
    }
}
