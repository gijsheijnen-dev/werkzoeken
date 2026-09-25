<?php declare(strict_types=1); ?>
<p class="result-count">
    <?= count($vacancies) ?> <?= count($vacancies) === 1 ? 'vacature' : 'vacatures' ?> gevonden
</p>

<?php if ($vacancies === []): ?>
    <p>Geen vacatures gevonden. Probeer een andere zoekterm of plaats.</p>
<?php else: ?>
    <ul class="vacancy-list">
        <?php foreach ($vacancies as $vacancy): ?>
            <li class="vacancy-card">
                <h2><?= e($vacancy->title) ?></h2>
                <p><?= e($vacancy->companyName) ?> &middot; <?= e($vacancy->location) ?></p>
                <a class="button" href="<?= e(sprintf('/vacature.php?id=%d', $vacancy->id)) ?>">Bekijk vacature</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
