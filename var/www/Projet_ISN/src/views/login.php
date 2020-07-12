<div class="connexion_container">
    <h2>Connexion</h2>
    <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
    <?php endif ?>
    <form action="" method="post">
        <label for="POST-name">Nom d'utilisateur :</label>
        <input id="POST-name" type="text" name="username" autofocus required>
        <label for="POST-name">Mot de passe :</label>
        <input id="POST-name" type="password" name="password" required>
        <button class="submit" type="submit">Se connecter</buttton>
    </form>
</div>