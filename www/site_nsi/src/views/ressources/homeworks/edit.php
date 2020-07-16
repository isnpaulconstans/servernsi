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
    <label for="id">Titre :</label>
    <input type="text" name="title" value="<?= $homework->title ?>" required>
    <label for="id">Date :</label>
    <input id="date" type="date" name="date" min="<?= $today ?>" value="<?= $homework->date ?>" required>
    <p>Classes :</p>
    <?php foreach ($class_list as $class): ?>
    <div>
      <input type="checkbox" id="<?= $class ?>" name="class[]" value="<?= $class ?>" <?= in_array($class, $homework->class) ? 'checked' : '' ?>>
      <label for="<?= $class ?>"><?= $class ?></label>
    </div>
    <?php endforeach ?>
    <button class="submit" type="submit">Enregistrer</button>
</form>
<form action="" method="post">
    <input type="hidden" name="del" value="<?= $homework->id ?>">
    <button class="submit attention" type="submit">Supprimer le devoir maison</button>
</form>

<a class="button" href="/ressources/homeworks">Retour</a>
