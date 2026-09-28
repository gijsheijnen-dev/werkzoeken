<?php

declare(strict_types=1);

use App\Admin\AdminRepository;
use App\Admin\AdminSession;
use App\Admin\Authenticator;
use App\Admin\LoginAttemptRepository;
use App\Admin\LoginThrottle;
use App\Database\ConnectionFactory;
use App\Database\DatabaseConfig;
use App\Http\FlashMessage;
use App\Http\Redirect;
use App\Http\Session;
use App\Security\CsrfToken;

$env = require dirname(__DIR__, 2) . '/src/bootstrap.php';

Session::start();

if (AdminSession::currentUsername() !== null) {
    Redirect::to(AdminSession::OVERVIEW_URL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CsrfToken::isValid($_POST['csrf_token'] ?? null) === false) {
        FlashMessage::set('login_error', 'Je sessie is verlopen. Probeer het opnieuw.');
        Redirect::to(AdminSession::LOGIN_URL);
    }

    $pdo = ConnectionFactory::create(DatabaseConfig::fromEnv($env));
    $throttle = new LoginThrottle(new LoginAttemptRepository($pdo));
    $ipAddress = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

    if ($throttle->isBlocked($ipAddress) === true) {
        FlashMessage::set('login_error', sprintf(
            'Te veel mislukte inlogpogingen. Probeer het over %d minuten opnieuw.',
            LoginThrottle::WINDOW_MINUTES,
        ));
        Redirect::to(AdminSession::LOGIN_URL);
    }

    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    $admin = (new Authenticator(new AdminRepository($pdo)))->attempt($username, $password);

    if ($admin === null) {
        $throttle->recordFailure($ipAddress);
        FlashMessage::set('login_error', 'Onjuiste gebruikersnaam of wachtwoord.');
        FlashMessage::set('login_username', $username);
        Redirect::to(AdminSession::LOGIN_URL);
    }

    $throttle->clear($ipAddress);
    AdminSession::login($admin);
    Redirect::to(AdminSession::OVERVIEW_URL);
}

$csrfToken = CsrfToken::get();
$loginError = FlashMessage::pull('login_error');
$loginNotice = FlashMessage::pull('login_notice');
$oldUsername = FlashMessage::pull('login_username') ?? '';

$templates = dirname(__DIR__, 2) . '/templates';

require $templates . '/layout/header.php';
require $templates . '/admin/login.php';
require $templates . '/layout/footer.php';
