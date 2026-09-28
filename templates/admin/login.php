<?php declare(strict_types=1); ?>
<h1>Inloggen beheer</h1>

<?php if ($loginNotice !== null): ?>
    <p class="alert alert-success" role="status"><?= e($loginNotice) ?></p>
<?php endif; ?>
<?php if ($loginError !== null): ?>
    <p class="alert alert-error" role="alert"><?= e($loginError) ?></p>
<?php endif; ?>

<form class="login-form" method="post" action="/beheer/inloggen.php">
    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

    <div class="form-field">
        <label for="login-username">Gebruikersnaam</label>
        <input id="login-username" name="username" type="text" autocomplete="username" required
               maxlength="50" value="<?= e($oldUsername) ?>">
    </div>

    <div class="form-field">
        <label for="login-password">Wachtwoord</label>
        <input id="login-password" name="password" type="password" autocomplete="current-password" required>
    </div>

    <div class="form-actions">
        <button type="submit">Inloggen</button>
    </div>
</form>
