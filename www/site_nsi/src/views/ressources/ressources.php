<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif ?>.
<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif ?>

<?php if ($ressources): ?>
<table>
    <thead>
        <tr>
            <th>Titre</th>
            <?php if ($allow_edit): ?>
            <th>Action</th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ressources as $ressource): ?>
        <tr>
            <td><a href="/file?t=<?= $ressource_type ?>&amp;f=<?= rawurlencode($ressource->file) ?>"><?= $ressource->title ?></a></td>
            <?php if ($allow_edit): ?>
            <td><a class="button" href="/<?= $tab ?>/edit?t=<?= $ressource_type ?>&amp;id=<?= $ressource->id ?>">Modifier</a></td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
        <tr class="tr-important">
            <td colspan="<?= $allow_edit ? 2 : 1 ?>">Total : <?= $ressources_count ?></td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<h2>Il n'y a pas de ressource disponible.</h2>
<?php endif ?>
<?php if ($allow_edit): ?>
<h2>Ajouter un<?= $ressource_word === 'activité' ? 'e' : '' ?> <?= $ressource_word ?> au format PDF</h2>
<form method="post" enctype="multipart/form-data">
    <label for="title">Titre :</label>
    <input id="title" type="text" name="title" required>
    <input type="file" id="file" name="pdf_file" accept=".pdf,application/pdf" required>
    <div>
        <button>Ajouter le cours</button>
    </div>
</form>
<?php endif ?>
