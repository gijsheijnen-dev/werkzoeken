<?php

declare(strict_types=1);

namespace App\Admin;

interface AdminRepositoryInterface
{
    public function findByUsername(string $username): ?Admin;
}
