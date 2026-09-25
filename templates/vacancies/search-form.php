<?php

declare(strict_types=1);

use App\Vacancy\VacancySearchCriteria;

?>
<form class="search-form" method="get" action="/" role="search">
    <label>
        Wat
        <input type="search" name="wat" value="<?= e($criteria->what) ?>"
               maxlength="<?= VacancySearchCriteria::MAX_LENGTH ?>" placeholder="Functie, trefwoord of bedrijf">
    </label>
    <label>
        Waar
        <input type="search" name="waar" value="<?= e($criteria->where) ?>"
               maxlength="<?= VacancySearchCriteria::MAX_LENGTH ?>" placeholder="Plaats">
    </label>
    <button type="submit">Zoeken</button>
    <?php if ($criteria->hasWhat() || $criteria->hasWhere()): ?>
        <a href="/">Wis zoekopdracht</a>
    <?php endif; ?>
</form>
