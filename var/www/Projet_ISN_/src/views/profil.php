<h2><strong><?= $user->last_name ?> <?= $user->first_name ?></strong> (<?= $user->username ?>)</h2>
<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif ?>
<form action="" method="post">
    <label for="theme">Thème : </label>
    <select name="theme" required>
        <option value="0"<?= $user->theme === 0 ? ' selected' : '' ?>>Clair</option>
        <option value="1"<?= $user->theme === 1 ? ' selected' : '' ?>>Sombre</option>
        <option value="2"<?= $user->theme === 2 ? ' selected' : '' ?>>Hacker</option>
    </select>
    <button class="submit" type="submit">Enregistrer</button>
</form>
<a class="button" href="/edit_pass">Modifier le mot de passe</a>
<a class="button" href="/logout">Déconnexion</a>