<?php

declare(strict_types=1);

use App\Application\ApplicationRules;

?>
<form id="apply-form" class="apply-form" method="post" action="/solliciteer.php"
      enctype="multipart/form-data" novalidate hidden
      data-max-cv-bytes="<?= ApplicationRules::MAX_CV_BYTES ?>"
      data-max-cv-megabytes="<?= ApplicationRules::MAX_CV_MEGABYTES ?>"
      data-cv-extension="<?= e(ApplicationRules::CV_EXTENSION) ?>">
    <h2>Solliciteren op <?= e($vacancy->title) ?></h2>

    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
    <input type="hidden" name="vacancy_id" value="<?= $vacancy->id ?>">

    <div class="form-field">
        <label for="apply-name">Naam</label>
        <input id="apply-name" name="name" type="text" autocomplete="name" required
               maxlength="<?= ApplicationRules::MAX_NAME_LENGTH ?>"
               aria-describedby="apply-name-error">
        <p id="apply-name-error" class="field-error" aria-live="polite"></p>
    </div>

    <div class="form-field">
        <label for="apply-email">E-mailadres</label>
        <input id="apply-email" name="email" type="email" autocomplete="email" required
               maxlength="<?= ApplicationRules::MAX_EMAIL_LENGTH ?>"
               aria-describedby="apply-email-error">
        <p id="apply-email-error" class="field-error" aria-live="polite"></p>
    </div>

    <div class="form-field">
        <label for="apply-cv">CV</label>
        <input id="apply-cv" name="cv" type="file" required
               accept="<?= e(ApplicationRules::CV_ACCEPT) ?>"
               aria-describedby="apply-cv-hint apply-cv-error">
        <p id="apply-cv-hint" class="field-hint">Alleen PDF, maximaal <?= ApplicationRules::MAX_CV_MEGABYTES ?> MB.</p>
        <p id="apply-cv-error" class="field-error" aria-live="polite"></p>
    </div>

    <div class="form-field">
        <label for="apply-motivation">Motivatie (optioneel)</label>
        <textarea id="apply-motivation" name="motivation" rows="5"
                  maxlength="<?= ApplicationRules::MAX_MOTIVATION_LENGTH ?>"
                  aria-describedby="apply-motivation-hint apply-motivation-error"></textarea>
        <p id="apply-motivation-hint" class="field-hint">Maximaal <?= ApplicationRules::MAX_MOTIVATION_LENGTH ?> tekens.</p>
        <p id="apply-motivation-error" class="field-error" aria-live="polite"></p>
    </div>

    <p id="apply-status" class="form-status" role="status" aria-live="polite"></p>

    <div class="form-actions">
        <button type="submit">Verstuur sollicitatie</button>
        <button type="button" id="apply-cancel" class="button-secondary">Annuleren</button>
    </div>
</form>
