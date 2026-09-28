<?php

declare(strict_types=1);

use App\Admin\AdminSession;
use App\Application\ApplicationRepository;
use App\Application\ApplicationRules;
use App\Application\CvStorage;
use App\Database\ConnectionFactory;
use App\Database\DatabaseConfig;
use App\Http\Session;

$env = require dirname(__DIR__, 2) . '/src/bootstrap.php';

Session::start();
AdminSession::requireLogin();
session_write_close();

$applicationId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$repository = new ApplicationRepository(ConnectionFactory::create(DatabaseConfig::fromEnv($env)));
$storedCv = is_int($applicationId) ? $repository->findCv($applicationId) : null;

$cvStorage = new CvStorage(dirname(__DIR__, 2) . '/storage/uploads');
$path = $storedCv === null ? null : $cvStorage->pathFor($storedCv->filename);

if ($path === null || is_file($path) === false) {
    http_response_code(404);

    $errorMessage = 'Dit CV bestaat niet (meer).';
    $templates = dirname(__DIR__, 2) . '/templates';

    require $templates . '/layout/header.php';
    require $templates . '/errors/request-error.php';
    require $templates . '/layout/footer.php';
    exit;
}

$asciiFallbackName = preg_replace('/[^A-Za-z0-9._-]/', '_', $storedCv->originalName);

header('Content-Type: ' . ApplicationRules::CV_MIME_TYPE);
header(sprintf(
    'Content-Disposition: attachment; filename="%s"; filename*=UTF-8\'\'%s',
    $asciiFallbackName,
    rawurlencode($storedCv->originalName),
));
header('Content-Length: ' . filesize($path));
header('Cache-Control: private, no-store');

readfile($path);
