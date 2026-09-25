<?php

declare(strict_types=1);

namespace App\Vacancy;

use PDO;

final readonly class VacancyRepository implements VacancyRepositoryInterface
{
    private const SELECT_SUMMARY = '
        SELECT v.id, v.title, v.location, c.name AS company_name
        FROM vacancies v
        INNER JOIN companies c ON c.id = v.company_id';

    private const WHAT_CONDITION = '(
        v.title LIKE :what_title
        OR v.description LIKE :what_description
        OR v.tags LIKE :what_tags
        OR c.name LIKE :what_company
    )';

    private const WHERE_CONDITION = 'v.location LIKE :where_location';

    private const ORDER_BY = ' ORDER BY v.created_at DESC, v.id DESC';

    public function __construct(private PDO $pdo)
    {
    }

    public function search(VacancySearchCriteria $criteria): array
    {
        $conditions = [];
        $parameters = [];

        if ($criteria->hasWhat()) {
            $pattern = $this->containsPattern($criteria->what);
            $conditions[] = self::WHAT_CONDITION;
            $parameters += [
                'what_title' => $pattern,
                'what_description' => $pattern,
                'what_tags' => $pattern,
                'what_company' => $pattern,
            ];
        }

        if ($criteria->hasWhere()) {
            $conditions[] = self::WHERE_CONDITION;
            $parameters['where_location'] = $this->containsPattern($criteria->where);
        }

        $statement = $this->pdo->prepare(self::SELECT_SUMMARY . $this->whereClause($conditions) . self::ORDER_BY);
        $statement->execute($parameters);

        return array_map(VacancySummary::fromRow(...), $statement->fetchAll());
    }

    /**
     * @param list<string> $conditions
     */
    private function whereClause(array $conditions): string
    {
        return $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);
    }

    private function containsPattern(string $term): string
    {
        return '%' . addcslashes($term, '\\%_') . '%';
    }
}
