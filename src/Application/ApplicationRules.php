<?php

declare(strict_types=1);

namespace App\Application;

/**
 * Class met all validatie regels voor het aanmaken van een sollicitatie.
 * Deze waarden worden gebruikt in de frontend validatie, dus je kunt ze
 * hier wijzigen.
 */
final class ApplicationRules
{
    public const MAX_NAME_LENGTH = 100;
    public const MAX_EMAIL_LENGTH = 255;
    public const MAX_MOTIVATION_LENGTH = 1000;

    public const MAX_CV_MEGABYTES = 2;
    public const MAX_CV_BYTES = self::MAX_CV_MEGABYTES * 1024 * 1024;
    public const CV_EXTENSION = 'pdf';
    public const CV_MIME_TYPE = 'application/pdf';
    public const CV_ACCEPT = '.' . self::CV_EXTENSION . ',' . self::CV_MIME_TYPE;

    private function __construct()
    {
    }
}
