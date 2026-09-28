<?php

declare(strict_types=1);

namespace App\Application;

interface ApplicationRepositoryInterface
{
    public function save(ApplicationSubmission $submission, string $cvFilename): int;

    /**
     * @return list<ApplicationOverview>
     */
    public function findAllForOverview(): array;
}
