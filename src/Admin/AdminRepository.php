<?php

declare(strict_types=1);

namespace App\Admin;

use PDO;

//repository die wordt gebruikt om de juiste user op te zoeken en de bijbehorende admin DTO terug te geven
final readonly class AdminRepository implements AdminRepositoryInterface
{
    private const SELECT_BY_USERNAME = 'SELECT id, username, password_hash FROM admins WHERE username = :username';

    public function __construct(private PDO $pdo)
    {
    }

    public function findByUsername(string $username): ?Admin
    {
        $statement = $this->pdo->prepare(self::SELECT_BY_USERNAME);
        $statement->execute(['username' => $username]);
        $row = $statement->fetch();

        return $row === false ? null : Admin::fromRow($row);
    }
}
