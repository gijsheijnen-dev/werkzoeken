<?php

declare(strict_types=1);

namespace App\Vacancy;

interface VacancyRepositoryInterface
{
    /**
     * @return list<VacancySummary>
     */
    public function search(VacancySearchCriteria $criteria): array;
}
