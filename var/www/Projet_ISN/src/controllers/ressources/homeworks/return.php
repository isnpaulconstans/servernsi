<?php
require_once MODELS_PATH . 'HomeworkDatabase.php';
require_once CLASSES_PATH . 'Homework.php';

$error    = null;
$success  = null;
$returned = false;
if (empty($_GET['id'])) {
    return;
}

$homework_db = new HomeworkDatabase;
$id = $_GET['id'];

if (!$homework = $homework_db->get($id)) {
    $error = 'Le devoir maison demandé n\'existe pas.';
    return;
}

$user = $_SESSION['user'];
if (array_key_exists($user->id, $homework->returned)) {
    $returned = true;
}

// Rend le devoir maison
if (!$returned &&
    !empty($_FILES['file']) &&
    $_FILES['file']['error'] === 0
) {
    if ($_FILES['file']['size'] > 1000000) {
        $error = 'Le fichier est trop lourd.';
        return;
    }

    $filename = $_FILES['file']['name'];
    $file = pathinfo($filename);
    // Le format du fichier est autorisé
    if (!in_array($file['extension'], CONFIG['production']['extension']) ||
        !in_array($_FILES['file']['type'], CONFIG['production']['mime_type'])
    ) {
        $error = 'Le format du fichier est incorrect.';
        return;
    }

    $filename = $user->username . '.' . $file['extension'];
    $file = DATA_PATH . 'homework' . DIRECTORY_SEPARATOR .
        $homework->prod_path . DIRECTORY_SEPARATOR . $filename;

    // Déplace le fichier du répertoire temporaire vers le répertoire associé à
    // la ressource.
    if (!move_uploaded_file($_FILES['file']['tmp_name'],
        $file)
    ) {
        $error = 'Erreur lors de l\'envoi du devoir maison.';
        return;
    }

    // Ajoute la nouvelle ressource à la base de données.
    if (!$homework_db->add_return($homework,
        $user->id, $filename)
    ) {
        unlink($file); // Supprime le fichier en cas d'erreur.
        $error = 'Erreur lors de l\'envoi du devoir maison.';
        return;
    }

    $homework = $homework_db->get($homework->id);
    $returned = true;
    $success = 'Devoir rendu.';
    return;
}

// Supprime le devoir maison
if (!empty($_POST['del'])) {
    if ($_POST['del'] != $homework->returned[$user->id]) {
        $error = 'Le devoir maison spécifié n\'éxiste pas.';
        return;
    }

    if (!unlink(DATA_PATH . 'homework' . DIRECTORY_SEPARATOR .
        $homework->prod_path . DIRECTORY_SEPARATOR .
        $homework->returned[$user->id])
    ) {
        $error = 'Une erreur s\'est produite lors de la ' .
        'suppression du devoir maison.';
        return;
    }
    if (!$homework_db->del_return($homework, $user->id)) {
        $error = 'Une erreur s\'est produite lors de la ' .
            'suppression du devoir maison.';
        return;
    }

    $success = 'Le devoir maison a été supprimé.';
    $returned = false;
}