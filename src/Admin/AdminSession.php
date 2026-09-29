<?php

declare(strict_types=1);

namespace App\Admin;

use App\Http\Redirect;
use App\Http\Session;

final class AdminSession
{
    public const LOGIN_URL = '/beheer/inloggen.php';
    public const OVERVIEW_URL = '/beheer/';

    public const IDLE_TIMEOUT_SECONDS = 30 * 60;

    private const SESSION_KEY = 'admin';

    public static function login(Admin $admin): void
    {
        Session::assertStarted();

        /**
         * Altijd een nieuw sessionId genereren, zodat oude sessies altijd verloren gaan.
         */
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

    /**
     * deze methode wordt ingezet op pagina's waarvoor je ingelogd moet zijn.
     * Hieronder valt ook het downloaden van de CV's die achter een login zitten.
     * De rechtstreekse download link werkt dus niet als je niet bent ingelogd.
     **/
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

        /**
         * Hier geen session_destroy, omdat er nog andere sessies draaien. (Bijvoorbeeld voor FlashMessage)
         */
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }

    /**
     * @param array|null $admin
     * @return bool
     */
    private static function isValidSessionData(?array $admin): bool
    {
        return is_int($admin['id'] ?? null)
            && is_string($admin['username'] ?? null)
            && is_int($admin['last_activity'] ?? null);
    }
}
