<?php

declare(strict_types=1);

namespace App\Application;

final class ApplicationValidator
{
    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $files
     */
    public function validate(int $vacancyId, array $input, array $files): ApplicationValidationResult
    {
        $name = $this->textValue($input, 'name');
        $email = $this->textValue($input, 'email');
        $motivation = $this->textValue($input, 'motivation');

        $errors = array_filter([
            'name' => $this->nameError($name),
            'email' => $this->emailError($email),
            'motivation' => $this->motivationError($motivation),
        ], static fn (?string $error): bool => $error !== null);

        try {
            $cv = $this->uploadedCv($files['cv'] ?? null);
        } catch (InvalidCvException $exception) {
            $errors['cv'] = $exception->getMessage();
        }

        if ($errors !== []) {
            return ApplicationValidationResult::invalid($errors);
        }

        return ApplicationValidationResult::valid(new ApplicationSubmission(
            vacancyId: $vacancyId,
            name: $name,
            email: $email,
            motivation: $motivation === '' ? null : $motivation,
            cv: $cv,
        ));
    }

    /**
     * @param array<string, mixed> $input
     */
    private function textValue(array $input, string $key): string
    {
        $value = $input[$key] ?? '';

        if (is_string($value) === false) {
            return '';
        }

        return trim(str_replace("\r\n", "\n", $value));
    }

    private function nameError(string $name): ?string
    {
        if ($name === '') {
            return 'Vul je naam in.';
        }

        if (mb_strlen($name) > ApplicationRules::MAX_NAME_LENGTH) {
            return sprintf('Je naam mag maximaal %d tekens bevatten.', ApplicationRules::MAX_NAME_LENGTH);
        }

        return null;
    }

    private function emailError(string $email): ?string
    {
        if ($email === '') {
            return 'Vul je e-mailadres in.';
        }

        if (mb_strlen($email) > ApplicationRules::MAX_EMAIL_LENGTH) {
            return sprintf('Je e-mailadres mag maximaal %d tekens bevatten.', ApplicationRules::MAX_EMAIL_LENGTH);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return 'Vul een geldig e-mailadres in.';
        }

        return null;
    }

    private function motivationError(string $motivation): ?string
    {
        if (mb_strlen($motivation) > ApplicationRules::MAX_MOTIVATION_LENGTH) {
            return sprintf('Je motivatie mag maximaal %d tekens bevatten.', ApplicationRules::MAX_MOTIVATION_LENGTH);
        }

        return null;
    }

    private function uploadedCv(mixed $file): UploadedCv
    {
        if (is_array($file) === false) {
            throw new InvalidCvException('Kies een CV om te uploaden.');
        }

        return UploadedCv::fromUpload($file);
    }
}
