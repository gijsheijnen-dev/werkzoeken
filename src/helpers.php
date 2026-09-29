<?php

declare(strict_types=1);

/**
 * functie om niet toegestane characters te escapen en XSS te voorkomen;
 *
 * @param string $value
 * @return string
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Functie die de eerste x aantal characters van een string toont.
 */
function excerpt(string $text, int $maxLength): string
{
    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }

    return mb_substr($text, 0, $maxLength) . '…';
}
