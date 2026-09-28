<?php

declare(strict_types=1);

use App\Admin\AdminSession;
use App\Http\FlashMessage;
use App\Http\Redirect;
use App\Http\Session;
use App\Security\CsrfToken;

require dirname(__DIR__, 2) . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);

    $errorMessage = 'Uitloggen kan alleen via de uitlogknop.';
    $templates = dirname(__DIR__, 2) . '/templates';

    require $templates . '/layout/header.php';
    require $templates . '/errors/request-error.php';
    require $templates . '/layout/footer.php';
    exit;
}

Session::start();

if (CsrfToken::isValid($_POST['csrf_token'] ?? null) === false) {
    Redirect::to(AdminSession::OVERVIEW_URL);
}

AdminSession::logout();
FlashMessage::set('login_notice', 'Je bent uitgelogd.');
Redirect::to(AdminSession::LOGIN_URL);
