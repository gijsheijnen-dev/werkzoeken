<?php declare(strict_types=1); ?>
<div class="admin-bar">
    <p>Ingelogd als <strong><?= e($username) ?></strong></p>
    <form method="post" action="/beheer/uitloggen.php">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <button type="submit" class="button-secondary">Uitloggen</button>
    </form>
</div>

<h1>Sollicitaties</h1>

<p class="result-count">
    <?= count($applications) ?> <?= count($applications) === 1 ? 'sollicitatie' : 'sollicitaties' ?>
</p>

<?php if ($applications === []): ?>
    <p>Er zijn nog geen sollicitaties binnengekomen.</p>
<?php else: ?>
    <ul class="application-list">
        <?php foreach ($applications as $application): ?>
            <li class="application-card">
                <h2><?= e($application->name) ?></h2>
                <p class="application-meta">
                    <?= e($application->createdAt->format('d-m-Y H:i')) ?> &middot;
                    <a href="<?= e(sprintf('/vacature.php?id=%d', $application->vacancyId)) ?>"><?= e($application->vacancyTitle) ?></a>
                    bij <?= e($application->companyName) ?>
                </p>
                <p><a href="mailto:<?= e($application->email) ?>"><?= e($application->email) ?></a></p>
                <p class="application-motivation">
                    <?= $application->motivation === null ? '-' : e(excerpt($application->motivation, 100)) ?>
                </p>
                <a class="button" href="<?= e(sprintf('/beheer/cv.php?id=%d', $application->id)) ?>">CV downloaden</a>
                <span class="field-hint"><?= e($application->cvOriginalName) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
