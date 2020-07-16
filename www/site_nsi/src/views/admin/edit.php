<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php return ?>
<?php endif ?>
<?php if ($warning): ?>
<div class="error"><?= $warning ?></div>
<?php endif ?>
<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif ?>
<?php if ($msg_copy): ?>
<div class="success"><?= $msg_copy ?>
    <button class="copy-btn">
        <i class="octicon octicon-clippy"></i>
    </button>
</div>
<script><?php require JS_PATH . 'copyTextToClipboard.php' ?></script>
<?php endif ?>

<form action="" method="post">
    <label for="last_name">Nom :</label>
    <input type="text" name="last_name" value="<?= $user->last_name ?>" required>
    <label for="first_name">Prénom :</label>
    <input type="text" name="first_name" value="<?= $user->first_name ?>" required>
    <?php if ($user->role === STUDENT): ?>
    <label for="class">Classe :</label>
    <input type="text" name="class" value="<?= $user->class ?>" required>
    <?php endif ?>
    <label for="username">Nom d'utilisateur :</label>
    <input type="text" name="username" value="<?= $user->username ?>" required>
    <input type="hidden" name="role" value="student">
    <button class="submit" type="submit">Enregistrer</button>
</form>
<form action="" method="post">
    <label for="new_password">Nouveau de passe (laissez vide pour un mot de passe aléatoire) :<label>
    <input type="password" name="new_password">
    <input type="hidden" name="regen_id" value="<?= $user->id ?>">
    <button class="submit" type="submit">Regénérer le mot de passe</button>
</form>
<form action="" method="post">
    <input type="hidden" name="del_id" value="<?= $user->id ?>">
    <button class="submit attention" type="submit">Supprimer l'utilisateur</button>
</form>

<a class="button" href="/admin">Retour</a>
