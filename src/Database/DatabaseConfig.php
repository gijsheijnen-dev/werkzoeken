<?php

declare(strict_types=1);

namespace App\Database;

use App\Config\Env;

final readonly class DatabaseConfig
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $name,
        public readonly string $user,
        #[\SensitiveParameter]
        public readonly string $password,
    ) {
    }

    public static function fromEnv(Env $env): self
    {
        return new self(
            host: $env->get('DB_HOST'),
            port: (int) $env->get('DB_PORT', '3306'),
            name: $env->get('DB_NAME'),
            user: $env->get('DB_USER'),
            password: $env->get('DB_PASS'),
        );
    }

    public function dsn(): string
    {
        return sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $this->host, $this->port, $this->name);
    }
}
