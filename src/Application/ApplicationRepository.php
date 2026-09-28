<?php

declare(strict_types=1);

namespace App\Application;

use PDO;

/**
 * Repository met queries om sollicitaties (of delen daarvan) te verwerken;
 */
final readonly class ApplicationRepository implements ApplicationRepositoryInterface
{
    private const INSERT = '
        INSERT INTO applications (vacancy_id, name, email, motivation, cv_filename, cv_original_name)
        VALUES (:vacancy_id, :name, :email, :motivation, :cv_filename, :cv_original_name)';

    private const SELECT_OVERVIEW = '
        SELECT a.id, a.created_at, a.vacancy_id, v.title AS vacancy_title, c.name AS company_name,
               a.name, a.email, a.motivation, a.cv_original_name
        FROM applications a
        INNER JOIN vacancies v ON v.id = a.vacancy_id
        INNER JOIN companies c ON c.id = v.company_id
        ORDER BY a.created_at DESC, a.id DESC';

    private const SELECT_CV = 'SELECT cv_filename, cv_original_name FROM applications WHERE id = :id';

    public function __construct(private PDO $pdo)
    {
    }

    public function save(ApplicationSubmission $submission, string $cvFilename): int
    {
        $statement = $this->pdo->prepare(self::INSERT);
        $statement->execute([
            'vacancy_id' => $submission->vacancyId,
            'name' => $submission->name,
            'email' => $submission->email,
            'motivation' => $submission->motivation,
            'cv_filename' => $cvFilename,
            'cv_original_name' => $submission->cv->originalName,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findAllForOverview(): array
    {
        $statement = $this->pdo->query(self::SELECT_OVERVIEW);

        return array_map(ApplicationOverview::fromRow(...), $statement->fetchAll());
    }

    public function findCv(int $applicationId): ?StoredCv
    {
        $statement = $this->pdo->prepare(self::SELECT_CV);
        $statement->execute(['id' => $applicationId]);
        $row = $statement->fetch();

        return $row === false ? null : StoredCv::fromRow($row);
    }
}
