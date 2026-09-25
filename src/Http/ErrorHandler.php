<?php

declare(strict_types=1);

namespace App\Http;

use ErrorException;
use Throwable;

final class ErrorHandler
{
    private bool $showDetails = false;

    public function register(): void
    {
        error_reporting(E_ALL);
        ini_set('log_errors', '1');
        $this->applyDisplaySetting();

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function setShowDetails(bool $showDetails): void
    {
        $this->showDetails = $showDetails;
        $this->applyDisplaySetting();
    }

    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public function handleException(Throwable $exception): void
    {
        error_log((string) $exception);

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo $this->showDetails
            ? '<pre>' . htmlspecialchars((string) $exception, ENT_QUOTES, 'UTF-8') . '</pre>'
            : '<h1>Er is iets misgegaan</h1><p>Probeer het later opnieuw.</p>';
    }

    private function applyDisplaySetting(): void
    {
        ini_set('display_errors', $this->showDetails ? '1' : '0');
    }
}
