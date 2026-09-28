<?php

declare(strict_types=1);

namespace App\Admin;

use App\Http\Redirect;
use App\Http\Session;

final class AdminSession
{
    public const LOGIN_URL = '/beheer/inloggen.php';

    public const IDLE_TIMEOUT_SECONDS = 30 * 60;

    private const SESSION_KEY = 'admin';

    public static function login(Admin $admin): void
    {
        Session::assertStarted();

        session_regenerate_id(true);

        $_SESSION[self::SESSION_KEY] = [
            'id' => $admin->id,
            'username' => $admin->username,
            'last_activity' => time(),
        ];
    }

    public static function currentUsername(): ?string
    {
        Session::assertStarted();

        $admin = $_SESSION[self::SESSION_KEY] ?? null;

        if (self::isValidSessionData($admin) === false) {
            return null;
        }

        if (time() - $admin['last_activity'] > self::IDLE_TIMEOUT_SECONDS) {
            self::logout();

            return null;
        }

        $_SESSION[self::SESSION_KEY]['last_activity'] = time();

        return $admin['username'];
    }

    public static function requireLogin(): string
    {
        $username = self::currentUsername();

        if ($username === null) {
            Redirect::to(self::LOGIN_URL);
        }

        return $username;
    }

    public static function logout(): void
    {
        Session::assertStarted();

        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }

    private static function isValidSessionData(mixed $admin): bool
    {
        return is_array($admin)
            && is_int($admin['id'] ?? null)
            && is_string($admin['username'] ?? null)
            && is_int($admin['last_activity'] ?? null);
    }
}
