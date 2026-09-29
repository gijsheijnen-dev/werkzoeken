<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Security headers die bepaalde ingangen voor aanvallen dichtzetten:
 * X-Content-Type-Options: nosniff zorgt ervoor dat html en javascript in geuploade bestanden niet wordt uitgevoerd.
 * X-Frame-Options: Geen Iframes toelaten om deze applicatie in te laden;
 * Referrer-Policy: Alleen referers doorsturen binnen de eigen applicatie; Zo lekken er geen URL's naar andere sites;
 * Content-Security-Policy: Alleen inladen via eigen domein (geen externe libraries, inline scripts/css etc.)
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
