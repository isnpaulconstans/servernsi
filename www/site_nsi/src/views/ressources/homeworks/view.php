<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif ?>
<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif ?>

<table>
    <thead>
        <tr>
            <th>Élève</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
        <tr>
            <?php if (array_key_exists($student->id, $homework->returned)): ?>
            <td class="good"><a href="/file?t=homework&amp;id=<?= $homework->id ?>&amp;f=<?= $student->id ?>"><?= $student->last_name . ' ' . $student->first_name ?></a></td>
            <?php else: ?>
            <td class="bad"><?= $student->last_name . ' ' . $student->first_name ?></td>
            <?php endif ?>
        </tr>
        <?php endforeach ?>
        <tr class="tr-important">
            <td colspan="1">Total : <?= $students_returned_count . ' / ' . $homework->students ?></td>
        </tr>
    </tbody>
</table>

<?php if (!empty($homework->returned)): ?>
<a class="button" href="/file?t=production&amp;f=<?= $homework->id ?>">Récupérer toutes les productions (zip)</a>
<?php endif ?>
<a class="button" href="/ressources/homeworks">Retour</a>
