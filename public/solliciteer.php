<?php

declare(strict_types=1);

use App\Application\ApplicationRepository;
use App\Application\ApplicationRules;
use App\Application\ApplicationValidator;
use App\Application\CvStorage;
use App\Database\ConnectionFactory;
use App\Database\DatabaseConfig;
use App\Http\FlashMessage;
use App\Http\Redirect;
use App\Http\Session;
use App\Security\CsrfToken;
use App\Vacancy\VacancyRepository;

$env = require dirname(__DIR__) . '/src/bootstrap.php';

$templates = dirname(__DIR__) . '/templates';

$renderErrorPage = static function (int $status, string $template, string $errorMessage = '') use ($templates): never {
    http_response_code($status);

    require $templates . '/layout/header.php';
    require $templates . '/errors/' . $template;
    require $templates . '/layout/footer.php';
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    $renderErrorPage(405, 'request-error.php', 'Solliciteren kan alleen via het sollicitatieformulier.');
}

if ($_POST === [] && $_FILES === [] && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $renderErrorPage(413, 'request-error.php', sprintf(
        'Je CV mag maximaal %d MB groot zijn. Ga terug en kies een kleiner bestand.',
        ApplicationRules::MAX_CV_MEGABYTES,
    ));
}

Session::start();

$pdo = ConnectionFactory::create(DatabaseConfig::fromEnv($env));

$vacancyId = filter_input(INPUT_POST, 'vacancy_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$vacancy = is_int($vacancyId) ? (new VacancyRepository($pdo))->findById($vacancyId) : null;

if ($vacancy === null) {
    $renderErrorPage(404, 'not-found.php');
}

$vacancyUrl = sprintf('/vacature.php?id=%d', $vacancy->id);

if (CsrfToken::isValid($_POST['csrf_token'] ?? null) === false) {
    FlashMessage::set('error', 'Je sessie is verlopen. Probeer het opnieuw.');
    Redirect::to($vacancyUrl . '#apply-feedback');
}

$result = (new ApplicationValidator())->validate($vacancy->id, $_POST, $_FILES);

if ($result->isValid() === false) {
    $oldInput = [];

    foreach (['name', 'email', 'motivation'] as $field) {
        $value = $_POST[$field] ?? '';
        $oldInput[$field] = is_string($value) ? $value : '';
    }

    FlashMessage::set('errors', $result->errors);
    FlashMessage::set('old_input', $oldInput);
    Redirect::to($vacancyUrl . '#apply-form');
}

$cvStorage = new CvStorage(dirname(__DIR__) . '/storage/uploads');
$cvFilename = $cvStorage->store($result->submission->cv);

try {
    (new ApplicationRepository($pdo))->save($result->submission, $cvFilename);
} catch (Throwable $exception) {
    $cvStorage->delete($cvFilename);

    throw $exception;
}

FlashMessage::set('success', 'Bedankt voor je sollicitatie!');
Redirect::to($vacancyUrl . '#apply-feedback');
