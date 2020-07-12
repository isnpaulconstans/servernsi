<?php if ($error): ?>
<div class="error"><?= $error ?></div>
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


<h2>Élèves</h2>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Classe</th>
            <th>Identifiant</th>
            <th>Action</th>
        </tr>
    </thead>
    <?php if ($student_list): ?>
    <tbody>
        <?php foreach ($student_list as $user): ?>
        <tr>
            <td><?= $user->last_name ?></td>
            <td><?= $user->first_name ?></td>
            <td><?= $user->class ?></td>
            <td><?= $user->username ?></td>
            <td><a class="button" href="/admin/edit?id=<?= $user->id ?>">Modifier</a></td>
        </tr>
        <?php endforeach ?>
        <tr class="tr-important">
            <td colspan="5">Total : <?= $student_count ?></td>
        </tr>
    </tbody>
    <?php endif ?>
    <tfoot>
        <tr>
            <form action="" method="post">
                <td><input type="text" name="last_name" required></td>
                <td><input type="text" name="first_name" required></td>
                <td><input type="text" name="class" required></td>
                <td><input type="text" name="username" required></td>
                <td>
                    <input type="hidden" name="role" value="0">
                    <button class="submit" type="submit">Ajouter</button>
                </td>
            </form>
        </tr>
    </tfoot>
</table>

<h2>Ajouter des élèves à partir d'un fichier CSV</h2>
<form method="post" enctype="multipart/form-data">
    <div>
        <label for="file">Choisir un fichier : </label>
        <input type="file" id="file" name="csv_file" accept=".csv,text/csv" required>
    </div>
    <div>
        <button>Envoyer le fichier</button>
    </div>
</form>
<?php if ($students_csv): ?>
<a class="button" href="/file?t=admin&f=students.csv">Télécharger le dernier fichier CSV des élèves</a>
<?php endif ?>

<h2>Professeurs</h2>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Identifiant</th>
            <th>Action</th>
        </tr>
    </thead>
    <?php if ($professor_list): ?>
    <tbody>
        <?php foreach ($professor_list as $user): ?>
        <tr>
            <td><?= $user->last_name ?></td>
            <td><?= $user->first_name ?></td>
            <td><?= $user->username ?></td>
            <td><a class="button" href="/admin/edit?id=<?= $user->id ?>">Modifier</a></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <?php endif ?>
    <tbody>
        <tr class="tr-important">
            <td colspan="4">Total : <?= $professor_count ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <form action="" method="post">
                <td><input type="text" name="last_name" required></td>
                <td><input type="text" name="first_name" required></td>
                <td><input type="text" name="username" required></td>
                <td>
                    <input type="hidden" name="role" value="1">
                    <button class="submit" type="submit">Ajouter</button>
                </td>
            </form>
        </tr>
    </tfoot>
</table>

<h2>Administrateurs</h2>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Identifiant</th>
            <th>Action</th>
        </tr>
    </thead>
    <?php if ($admin_list): ?>
    <tbody>
        <?php foreach ($admin_list as $user): ?>
        <tr>
            <td><?= $user->last_name ?></td>
            <td><?= $user->first_name ?></td>
            <td><?= $user->username ?></td>
            <td><a class="button" href="/admin/edit?id=<?= $user->id ?>">Modifier</a></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <?php endif ?>
    <tbody>
        <tr class="tr-important">
            <td colspan="4">Total : <?= $admin_count ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <form method="post">
                <td><input type="text" name="last_name" required></td>
                <td><input type="text" name="first_name" required></td>
                <td><input type="text" name="username" required></td>
                <td>
                    <input type="hidden" name="role" value="2">
                    <button class="submit" type="submit">Ajouter</button>
                </td>
            </form>
        </tr>
    </tfoot>
</table>
