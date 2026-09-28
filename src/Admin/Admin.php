<?php

declare(strict_types=1);

namespace App\Admin;

//Deze class return een DTO van de ingelogde user;
final readonly class Admin
{
    public function __construct(
        public int $id,
        public string $username,
        public string $passwordHash,
    ) {
    }

    /**
     * @param array{id: int|string, username: string, password_hash: string} $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            username: $row['username'],
            passwordHash: $row['password_hash'],
        );
    }
}
