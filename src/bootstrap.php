<?php

declare(strict_types=1);

use App\Config\Env;
use App\Http\ErrorHandler;
use App\Http\SecurityHeaders;

require __DIR__ . '/helpers.php';

/**
 * Simpele autoloading omdat composer niet gebruikt mag worden;
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (str_starts_with($class, $prefix) === false) {
        return;
    }

    $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
/**
 * De errorhandler wordt gestart en zet standaard het tonen van exceptions uit. Zo is er wel error handling
 * wanneer de .env wordt ingeladen, maar wordt dit wel gelogd.
 */
$errorHandler = new ErrorHandler();
$errorHandler->register();

$env = Env::fromFile(dirname(__DIR__) . '/.env');
$errorHandler->setShowDetails($env->get('APP_ENV', 'production') === 'development');

SecurityHeaders::send();

return $env;
