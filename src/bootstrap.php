<?php

declare(strict_types=1);

use App\Config\Env;
use App\Http\ErrorHandler;
use App\Http\SecurityHeaders;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$errorHandler = new ErrorHandler();
$errorHandler->register();

$env = Env::fromFile(dirname(__DIR__) . '/.env');
$errorHandler->setShowDetails($env->get('APP_ENV', 'production') === 'development');

SecurityHeaders::send();

return $env;
