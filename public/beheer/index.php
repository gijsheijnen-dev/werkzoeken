<?php

declare(strict_types=1);

use App\Admin\AdminSession;
use App\Application\ApplicationRepository;
use App\Database\ConnectionFactory;
use App\Database\DatabaseConfig;
use App\Http\Session;
use App\Security\CsrfToken;

$env = require dirname(__DIR__, 2) . '/src/bootstrap.php';

Session::start();

$username = AdminSession::requireLogin();
$csrfToken = CsrfToken::get();

$repository = new ApplicationRepository(ConnectionFactory::create(DatabaseConfig::fromEnv($env)));
$applications = $repository->findAllForOverview();

$templates = dirname(__DIR__, 2) . '/templates';

require $templates . '/layout/header.php';
require $templates . '/admin/applications.php';
require $templates . '/layout/footer.php';
