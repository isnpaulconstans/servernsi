<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif ?>
<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif ?>

<?php if ($homeworks): ?>
<table>
    <thead>
        <tr>
            <th>Titre</th>
            <th>Date</th>
            <?php if ($allow_edit): ?>
            <th>Rendu</th>
            <?php endif ?>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
	<?php
	    foreach ($homeworks as $homework):
	?>
	<tr <?php if ($student):?> class="<?= array_key_exists($user->id, $homework->returned) ? 'good' : 'bad' ?>"<?php endif?>>
	    <td><a href="/file?t=<?= $ressource_type ?>&amp;f=<?= rawurlencode($homework->file) ?>"><?= $homework->title ?></a></td>
            <td><?php $date = new DateTime($homework->date); echo $week[$date->format('N')] . ' ' . $date->format('j') . ' ' . $month[$date->format('n')] . ' ' . $date->format('Y'); ?></td>
            <?php if ($allow_edit): ?>
            <td><a class="button" href="/homeworks/view?id=<?= $homework->id ?>"><?php echo count($homework->returned) ?> / <?= $homework->students ?></a></td>
            <td><a class="button" href="/homeworks/edit?id=<?= $homework->id ?>">Modifier</a></td>
            <?php endif ?>
            <?php if ($student): ?>
            <td><a class="button" href="/homeworks/return?id=<?= $homework->id ?>">Rendre</a></td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
        <tr class="tr-important">
            <td colspan="<?= $allow_edit ? 4 : 3 ?>">Total : <?= $homeworks_count ?></td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<h2>Il n'y a aucun devoir maison.</h2>
<?php endif ?>

<?php if ($allow_edit): ?>
<h2>Ajouter un sujet de devoir maison</h2>
<form method="post" enctype="multipart/form-data">
    <label for="title">Titre :</label>
    <input id="title" type="text" name="title" required>
    <label for="date">Date :</label>
    <input id="date" type="date" name="date" min="<?= $today ?>" required>
    <p>Classes :</p>
    <?php foreach ($class_list as $class): ?>
    <div>
      <input type="checkbox" id="<?= $class ?>" name="class[]" value="<?= $class ?>">
      <label for="<?= $class ?>"><?= $class ?></label>
    </div>
    <?php endforeach ?>
    <input type="file" id="file" name="pdf_file" accept=".pdf,application/pdf" required>
    <div>
        <button>Ajouter le devoir maison</button>
    </div>
</form>
<?php endif ?>
