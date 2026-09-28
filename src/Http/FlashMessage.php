<?php

declare(strict_types=1);

namespace App\Http;

final class FlashMessage
{
    private const SESSION_KEY = 'flash_message';

    public static function set(string $key, mixed $value): void
    {
        Session::assertStarted();

        $_SESSION[self::SESSION_KEY][$key] = $value;
    }

    public static function pull(string $key): mixed
    {
        Session::assertStarted();

        $value = $_SESSION[self::SESSION_KEY][$key] ?? null;
        unset($_SESSION[self::SESSION_KEY][$key]);

        return $value;
    }
}
