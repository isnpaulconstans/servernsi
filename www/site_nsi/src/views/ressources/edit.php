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

<form action="" method="post">
    <label for="id">ID :</label>
    <input type="number" name="id" min="1" value="<?= $ressource->id ?>" required>
    <label for="id">Titre :</label>
    <input type="text" name="title" value="<?= $ressource->title ?>" required>
    <button class="submit" type="submit">Enregistrer</button>
</form>
<form action="" method="post">
    <input type="hidden" name="del" value="<?= $ressource->id ?>">
    <button class="submit attention" type="submit">Supprimer le cours</button>
</form>

<a class="button" href="/<?= $tab ?>/<?= $type_path ?>">Retour</a>
