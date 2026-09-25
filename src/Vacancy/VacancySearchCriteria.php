<?php

declare(strict_types=1);

namespace App\Vacancy;

final readonly class VacancySearchCriteria
{
    public const MAX_LENGTH = 100;

    public function __construct(
        public string $what = '',
        public string $where = '',
    ) {
    }

    /**
     * @param array<string, mixed> $query
     */
    public static function fromQuery(array $query): self
    {
        return new self(
            what: self::normalize($query['wat'] ?? ''),
            where: self::normalize($query['waar'] ?? ''),
        );
    }

    public function hasWhat(): bool
    {
        return $this->what !== '';
    }

    public function hasWhere(): bool
    {
        return $this->where !== '';
    }

    private static function normalize(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        return mb_substr(trim($value), 0, self::MAX_LENGTH);
    }
}
