<?php

declare(strict_types=1);

namespace App\Application;

use DateTimeImmutable;

final readonly class ApplicationOverview
{
    public function __construct(
        public int $id,
        public DateTimeImmutable $createdAt,
        public int $vacancyId,
        public string $vacancyTitle,
        public string $companyName,
        public string $name,
        public string $email,
        public ?string $motivation,
        public string $cvOriginalName,
    ) {
    }

    /**
     * @param array{
     *     id: int|string,
     *     created_at: string,
     *     vacancy_id: int|string,
     *     vacancy_title: string,
     *     company_name: string,
     *     name: string,
     *     email: string,
     *     motivation: ?string,
     *     cv_original_name: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            createdAt: new DateTimeImmutable($row['created_at']),
            vacancyId: (int) $row['vacancy_id'],
            vacancyTitle: $row['vacancy_title'],
            companyName: $row['company_name'],
            name: $row['name'],
            email: $row['email'],
            motivation: $row['motivation'],
            cvOriginalName: $row['cv_original_name'],
        );
    }
}
