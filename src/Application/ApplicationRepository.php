<?php

declare(strict_types=1);

namespace App\Application;

use PDO;

final readonly class ApplicationRepository implements ApplicationRepositoryInterface
{
    private const INSERT = '
        INSERT INTO applications (vacancy_id, name, email, motivation, cv_filename, cv_original_name)
        VALUES (:vacancy_id, :name, :email, :motivation, :cv_filename, :cv_original_name)';

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
}
