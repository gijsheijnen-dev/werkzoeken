<?php

declare(strict_types=1);

namespace App\Application;

use InvalidArgumentException;
use RuntimeException;

final readonly class CvStorage
{
    private const FILE_PERMISSIONS = 0640;

    public function __construct(private string $directory)
    {
        if (is_dir($directory) === false) {
            throw new RuntimeException(sprintf('CV storage directory "%s" does not exist.', $directory));
        }
    }

    public function store(UploadedCv $cv): string
    {
        $filename = bin2hex(random_bytes(16)) . '.' . ApplicationRules::CV_EXTENSION;
        $destination = $this->pathFor($filename);

        if (move_uploaded_file($cv->temporaryPath, $destination) === false) {
            throw new RuntimeException('Could not move the uploaded CV to the storage directory.');
        }

        chmod($destination, self::FILE_PERMISSIONS);

        return $filename;
    }

    public function delete(string $filename): void
    {
        $path = $this->pathFor($filename);

        if (is_file($path) === true) {
            unlink($path);
        }
    }

    public function pathFor(string $filename): string
    {
        //add D-modifier so filenames with \n will be invalidated.
        $pattern = '/^[a-f0-9]{32}\.' . preg_quote(ApplicationRules::CV_EXTENSION, '/') . '$/D';

        if (preg_match($pattern, $filename) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid CV filename "%s".', $filename));
        }

        return $this->directory . '/' . $filename;
    }
}
