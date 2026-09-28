<?php

declare(strict_types=1);

namespace App\Admin;

interface LoginAttemptRepositoryInterface
{
    public function countSince(string $ipAddress, int $minutes): int;

    public function add(string $ipAddress): void;

    public function deleteOlderThan(int $minutes): void;

    public function deleteForIp(string $ipAddress): void;
}
