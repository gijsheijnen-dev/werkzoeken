<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Security headers die bepaalde ingangen voor aanvallen dichtzetten:
 * bijvoorbeeld geen inline javascripts toe laten, de php versie header verwijderen zodat aanvallers je phpversie
 * niet kunnen achter halen. etc.
 */
final class SecurityHeaders
{
    private const HEADERS = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'Referrer-Policy' => 'same-origin',
        'Content-Security-Policy' => "default-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'",
    ];

    public static function send(): void
    {
        if (headers_sent()) {
            return;
        }

        header_remove('X-Powered-By');

        foreach (self::HEADERS as $name => $value) {
            header($name . ': ' . $value);
        }
    }
}
