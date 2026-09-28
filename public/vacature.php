<?php

declare(strict_types=1);

use App\Database\ConnectionFactory;
use App\Database\DatabaseConfig;
use App\Vacancy\VacancyRepository;

$env = require dirname(__DIR__) . '/src/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$repository = new VacancyRepository(ConnectionFactory::create(DatabaseConfig::fromEnv($env)));
$vacancy = is_int($id) ? $repository->findById($id) : null;

if ($vacancy === null) {
    http_response_code(404);
}

$templates = dirname(__DIR__) . '/templates';

require $templates . '/layout/header.php';
require $templates . ($vacancy === null ? '/errors/not-found.php' : '/vacancies/detail.php');
require $templates . '/layout/footer.php';
