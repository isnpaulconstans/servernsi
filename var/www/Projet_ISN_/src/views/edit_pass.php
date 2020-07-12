<div class="connexion_container">
    <h2>Modifier le mot de passe</h2>
    <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
    <?php endif ?>
    <form action="" method="post">
        <label for="old_pass">Ancien mot de passe :</label>
        <input id="old_pass" type="password" name="old_pass" autofocus required>
        <label for="new_pass">Nouveau mot de passe :</label>
        <input id="new_pass" type="password" name="new_pass" minlength="<?= $password_length ?>" required>
        <label for="confirm_pass">Confirmer mot de passe :</label>
        <input id="confirm_pass" type="password" name="confirm_pass" minlength="<?= $password_length ?>" required>
        <input type="submit" value="Enregistrer">
    </form>
</div>