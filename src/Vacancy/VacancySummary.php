<?php

declare(strict_types=1);

namespace App\Vacancy;

final readonly class VacancySummary
{
    public function __construct(
        public int $id,
        public string $title,
        public string $companyName,
        public string $location,
    ) {
    }

    /**
     * @param array{id: int|string, title: string, company_name: string, location: string} $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            title: $row['title'],
            companyName: $row['company_name'],
            location: $row['location'],
        );
    }
}
