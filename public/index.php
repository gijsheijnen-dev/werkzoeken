<?php

declare(strict_types=1);

use App\Database\ConnectionFactory;
use App\Database\DatabaseConfig;
use App\Vacancy\VacancyRepository;
use App\Vacancy\VacancySearchCriteria;

$env = require dirname(__DIR__) . '/src/bootstrap.php';

$repository = new VacancyRepository(ConnectionFactory::create(DatabaseConfig::fromEnv($env)));
$criteria = VacancySearchCriteria::fromQuery($_GET);
$vacancies = $repository->search($criteria);

$templates = dirname(__DIR__) . '/templates';

require $templates . '/layout/header.php';
require $templates . '/vacancies/search-form.php';
require $templates . '/vacancies/list.php';
require $templates . '/layout/footer.php';
