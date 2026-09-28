<?php

declare(strict_types=1);

namespace App\Application;

/**
 * DTO voor het ophalen een CV bij een sollicitatie;
 */
final readonly class StoredCv
{
    public function __construct(
        public string $filename,
        public string $originalName,
    ) {
    }

    /**
     * @param array{cv_filename: string, cv_original_name: string} $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            filename: $row['cv_filename'],
            originalName: $row['cv_original_name'],
        );
    }
}
