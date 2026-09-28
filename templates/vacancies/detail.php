<?php declare(strict_types=1); ?>
<a class="back-link" href="/">&larr; Terug naar overzicht</a>

<article class="vacancy-detail">
    <h1><?= e($vacancy->title) ?></h1>
    <p class="vacancy-meta"><?= e($vacancy->companyName) ?> &middot; <?= e($vacancy->location) ?></p>

    <div class="vacancy-description">
        <?= nl2br(e($vacancy->description)) ?>
    </div>

    <section class="vacancy-contact">
        <h2>Contactpersoon</h2>
        <p>
            <?= e($vacancy->contactName) ?><br>
            <a href="mailto:<?= e($vacancy->contactEmail) ?>"><?= e($vacancy->contactEmail) ?></a>
        </p>
    </section>

    <div id="apply-feedback">
        <?php if ($successMessage !== null): ?>
            <p class="alert alert-success" role="status"><?= e($successMessage) ?></p>
        <?php endif; ?>
        <?php if ($generalError !== null): ?>
            <p class="alert alert-error" role="alert"><?= e($generalError) ?></p>
        <?php endif; ?>
    </div>

    <button type="button" id="apply-button" aria-controls="apply-form"
            aria-expanded="<?= $formErrors === [] ? 'false' : 'true' ?>">Solliciteer</button>

    <?php require dirname(__DIR__) . '/applications/form.php'; ?>
</article>

<script src="/assets/js/back-link.js" defer></script>
<script src="/assets/js/apply-form.js" defer></script>
