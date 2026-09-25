<?php

declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class Env
{
    /**
     * @param array<string, string> $values
     */
    private function __construct(private readonly array $values)
    {
    }

    public static function fromFile(string $path): self
    {
        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('Env file "%s" is not readable.', $path));
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return new self(self::parseLines($lines));
    }

    public function get(string $key, ?string $default = null): string
    {
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }

        if ($default !== null) {
            return $default;
        }

        throw new RuntimeException(sprintf('Missing required env key "%s".', $key));
    }

    /**
     * @param list<string> $lines
     * @return array<string, string>
     */
    private static function parseLines(array $lines): array
    {
        $values = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $values[trim($key)] = self::unquote(trim($value));
        }

        return $values;
    }

    private static function unquote(string $value): string
    {
        $isQuoted = strlen($value) >= 2
            && ($value[0] === '"' || $value[0] === "'")
            && $value[-1] === $value[0];

        return $isQuoted ? substr($value, 1, -1) : $value;
    }
}
