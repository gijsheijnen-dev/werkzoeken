<?php

declare(strict_types=1);

namespace App\Application;

final readonly class ApplicationValidationResult
{
    /**
     * @param array<string, string> $errors
     */
    private function __construct(
        public ?ApplicationSubmission $submission,
        public array $errors,
    ) {
    }

    public static function valid(ApplicationSubmission $submission): self
    {
        return new self($submission, []);
    }

    /**
     * @param array<string, string> $errors
     */
    public static function invalid(array $errors): self
    {
        return new self(null, $errors);
    }

    public function isValid(): bool
    {
        return $this->submission !== null;
    }
}
