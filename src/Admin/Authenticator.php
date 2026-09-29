<?php

declare(strict_types=1);

namespace App\Admin;

final readonly class Authenticator
{
    private const DUMMY_HASH = '$2y$10$x/VP/dmwpF.STh6956Y87O1rFvGV4MpJPqmTDagodbHhpfNcQcW2K';

    public function __construct(private AdminRepositoryInterface $admins)
    {
    }

    /**
     * Er wordt altijd een password_verify uitgevoerd. Ook als de betreffende gebruiker niet wordt
     * gevonden. Dit voorkomt dat je gebruikersnamen kunt achterhalen.
     */
    public function attempt(string $username, #[\SensitiveParameter] string $password): ?Admin
    {
        $admin = $this->admins->findByUsername($username);

        $passwordMatches = password_verify($password, $admin?->passwordHash ?? self::DUMMY_HASH);

        if ($admin === null || $passwordMatches === false) {
            return null;
        }

        return $admin;
    }
}
