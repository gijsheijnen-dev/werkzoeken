<?php

declare(strict_types=1);

namespace App\Vacancy;

final readonly class VacancyDetail
{
    public function __construct(
        public int $id,
        public string $title,
        public string $companyName,
        public string $description,
        public string $location,
        public string $contactName,
        public string $contactEmail,
    ) {
    }

    /**
     * @param array{
     *     id: int|string,
     *     title: string,
     *     company_name: string,
     *     description: string,
     *     location: string,
     *     contact_name: string,
     *     contact_email: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            title: $row['title'],
            companyName: $row['company_name'],
            description: $row['description'],
            location: $row['location'],
            contactName: $row['contact_name'],
            contactEmail: $row['contact_email'],
        );
    }
}
