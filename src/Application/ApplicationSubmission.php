<?php

declare(strict_types=1);

namespace App\Application;

/**
 * DTO met hierin de sollicitatie die gaat worden opgeslagen.
 */
final readonly class ApplicationSubmission
{
    public function __construct(
        public int $vacancyId,
        public string $name,
        public string $email,
        public ?string $motivation,
        public UploadedCv $cv,
    ) {
    }
}
