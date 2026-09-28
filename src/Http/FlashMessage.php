<?php

declare(strict_types=1);

namespace App\Http;

/**
 * FlashMessager om resultaten van requests te tonen in de UI. De flash message wordt
 * opgeslagen in een sessie en wanneer hij getoond wordt, wordt de sessie gewist. Hierdoor komt
 * het resultaat maar 1x in beeld.
 */
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
