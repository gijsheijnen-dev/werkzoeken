<?php

declare(strict_types=1);

namespace App\Application;

use finfo;
use RuntimeException;

/**
 * Class die de geuploaded CV valideert en vervolgens zichzelf returned, of een exception throwed
 * als de pdf niet valide is.
 */
final readonly class UploadedCv
{
    private const MAX_ORIGINAL_NAME_LENGTH = 255;
    private const FALLBACK_ORIGINAL_NAME = 'cv.pdf';

    private function __construct(
        public string $temporaryPath,
        public string $originalName,
    ) {
    }

    public static function fromUpload(array $file): self
    {
        if (self::isSingleUpload($file) === false) {
            throw new InvalidCvException('Kies een CV om te uploaden.');
        }

        self::assertUploadSucceeded($file['error']);

        $temporaryPath = $file['tmp_name'];

        if (is_uploaded_file($temporaryPath) === false) {
            throw new InvalidCvException('Het uploaden is niet gelukt. Probeer het opnieuw.');
        }

        self::assertValidSize($temporaryPath);
        self::assertPdf($temporaryPath);

        return new self($temporaryPath, self::sanitizeOriginalName($file['name']));
    }

    private static function isSingleUpload(array $file): bool
    {
        return is_int($file['error'] ?? null)
            && is_string($file['tmp_name'] ?? null)
            && is_string($file['name'] ?? null);
    }

    private static function assertUploadSucceeded(int $error): void
    {
        $message = match ($error) {
            UPLOAD_ERR_OK => null,
            UPLOAD_ERR_NO_FILE => 'Kies een CV om te uploaden.',
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => self::tooLargeMessage(),
            UPLOAD_ERR_PARTIAL => 'Het uploaden is niet gelukt. Probeer het opnieuw.',
            default => throw new RuntimeException(sprintf('CV upload failed with error code %d.', $error)),
        };

        if ($message !== null) {
            throw new InvalidCvException($message);
        }
    }

    private static function assertValidSize(string $path): void
    {
        $size = filesize($path);

        if ($size === false) {
            throw new RuntimeException('Could not determine the size of the uploaded CV.');
        }

        if ($size === 0) {
            throw new InvalidCvException('Het gekozen bestand is leeg.');
        }

        if ($size > ApplicationRules::MAX_CV_BYTES) {
            throw new InvalidCvException(self::tooLargeMessage());
        }
    }

    private static function assertPdf(string $path): void
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        if ($mimeType !== ApplicationRules::CV_MIME_TYPE) {
            throw new InvalidCvException('Je CV moet een PDF-bestand zijn.');
        }
    }

    private static function sanitizeOriginalName(string $name): string
    {
        $name = basename(str_replace('\\', '/', mb_scrub($name, 'UTF-8')));
        $name = trim((string) preg_replace('/[\x00-\x1F\x7F]/u', '', $name));
        $name = mb_substr($name, 0, self::MAX_ORIGINAL_NAME_LENGTH);

        return $name === '' ? self::FALLBACK_ORIGINAL_NAME : $name;
    }

    private static function tooLargeMessage(): string
    {
        return sprintf('Je CV mag maximaal %d MB groot zijn.', ApplicationRules::MAX_CV_MEGABYTES);
    }
}
