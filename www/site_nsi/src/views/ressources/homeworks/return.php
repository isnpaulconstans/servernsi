<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif ?>
<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif ?>

<?php if ($returned): ?>
<h2>Vous avez rendu ce devoir maison.</h2>
<form action="" method="post">
    <input type="hidden" name="del" value="<?= $homework->returned[$user->id] ?>">
    <button class="submit attention" type="submit">Supprimer le devoir maison</button>
</form>
<?php else: ?>
<h2>Vous n'avez pas encore rendu ce devoir maison.</h2>
<form method="post" enctype="multipart/form-data">
<input type="file" id="file" name="file" accept="<?php
foreach (CONFIG['production']['extension'] as $extension) {
	echo '.' . $extension . ',';
}
foreach (CONFIG['production']['mime_type'] as $mime) {
	echo $mime . ',';
}
?>" required>
    <div>
        <button>Rendre le devoir maison</button>
    </div>
</form>
<?php endif ?>

<a class="button" href="/homeworks">Retour</a>
