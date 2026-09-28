<?php

declare(strict_types=1);

namespace App\Admin;

/**
 * Deze class wordt aangeroepen om blokkades aan te maken.
 */
final readonly class LoginThrottle
{
    public const MAX_FAILED_ATTEMPTS = 5;

    public const WINDOW_MINUTES = 15;

    public function __construct(private LoginAttemptRepositoryInterface $attempts)
    {
    }

    public function isBlocked(string $ipAddress): bool
    {
        return $this->attempts->countSince($ipAddress, self::WINDOW_MINUTES) >= self::MAX_FAILED_ATTEMPTS;
    }

    public function recordFailure(string $ipAddress): void
    {
        $this->attempts->deleteOlderThan(self::WINDOW_MINUTES);
        $this->attempts->add($ipAddress);
    }

    public function clear(string $ipAddress): void
    {
        $this->attempts->deleteForIp($ipAddress);
    }
}
