<?php

declare(strict_types=1);

namespace App\Admin;

use PDO;

/**
 * Deze class houdt je loginpogingen bij in de database. Deze pogingen worden weer gewist op het moment dat je
 * succesvol inlogt, of wanneer de blokkade periode is verlopen.
 */
final readonly class LoginAttemptRepository implements LoginAttemptRepositoryInterface
{
    private const COUNT_SINCE = '
        SELECT COUNT(*) FROM login_attempts
        WHERE ip_address = :ip_address AND attempted_at > NOW() - INTERVAL :minutes MINUTE';

    private const INSERT = 'INSERT INTO login_attempts (ip_address) VALUES (:ip_address)';

    private const DELETE_OLDER_THAN = 'DELETE FROM login_attempts WHERE attempted_at <= NOW() - INTERVAL :minutes MINUTE';

    private const DELETE_FOR_IP = 'DELETE FROM login_attempts WHERE ip_address = :ip_address';

    public function __construct(private PDO $pdo)
    {
    }

    public function countSince(string $ipAddress, int $minutes): int
    {
        $statement = $this->pdo->prepare(self::COUNT_SINCE);
        $statement->execute(['ip_address' => $ipAddress, 'minutes' => $minutes]);

        return (int) $statement->fetchColumn();
    }

    public function add(string $ipAddress): void
    {
        $this->pdo->prepare(self::INSERT)->execute(['ip_address' => $ipAddress]);
    }

    public function deleteOlderThan(int $minutes): void
    {
        $this->pdo->prepare(self::DELETE_OLDER_THAN)->execute(['minutes' => $minutes]);
    }

    public function deleteForIp(string $ipAddress): void
    {
        $this->pdo->prepare(self::DELETE_FOR_IP)->execute(['ip_address' => $ipAddress]);
    }
}
