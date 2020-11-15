<?php
require_once FUNCTIONS_PATH . 'global.php';
require_once MODELS_PATH . 'UserDatabase.php';
require_once MODELS_PATH . 'HomeworkDatabase.php';
require_once CLASSES_PATH . 'Homework.php';

$error   = null;
$success = null;
$warning = null;

$user_db = new UserDatabase;
if ($class_list = $user_db->get_classes()) {
    $class_list = array_slice($class_list, 1);
} else {
    $class_list = null;
}

$today = date('Y-m-d');

// L'ID ('id') est passé par l'URI.
if (empty($_GET['id'])) {
    $error = 'Requête invalide.';
    return;
}
$id = (int)$_GET['id'];

if ($id < 0) {
    $error = '\'' . htmlentities($type) . '\' n\'est pas un type de ' .
        'ressource valide.';
    return;
}
// Créer la connexion à la base de données.
$homework_db = new HomeworkDatabase;

// Le devoir maison existe et `$homework` est une instance de Homework.
if (!$homework = $homework_db->get($id)) {
    $error = 'La ressource demandée n\'exite pas.';
    return;
}

// Suppression
if (!empty($_POST['del'])) {
    // La ressource à supprimer correspond à celle passé en GET.
    if ((int)$_POST['del'] != $id) {
        $error = 'La ressource ne correspond pas.';
        return;
    }

    $homework_db->pdo->beginTransaction();
    if (!$homework_db->del($homework)) {
        // Annule les modifications dans la base de données.
        $homework_db->pdo->rollBack();
        $error = 'Une erreur est survenue lors de la suppression du devoir ' .
            'maison';
        return;
    }

    $file = DATA_PATH . 'homework' . DIRECTORY_SEPARATOR . $homework->file;
    if (file_exists($file)) {
        unlink($file);
    }

    if (file_exists($file)) {
        // Annule les modifications dans la base de données.
        $homework_db->pdo->rollBack();
        $error = 'Une erreur est survenue lors de la suppression du devoir ' .
            'maison';
        return;
    }

    del_dir(DATA_PATH . 'homework' . DIRECTORY_SEPARATOR .
        $homework->prod_path);
    // Valide les modifications dans la base de données.
    $homework_db->pdo->commit();

    // Redirection vers la liste des ressources associés.
    header("Location: /homeworks");
    exit;
}

// Modification
if (isset($_POST['title']) &&
    isset($_POST['date'])
) {
    $new_title = htmlentities(trim($_POST['title']));
    $new_date  = htmlentities($_POST['date']);

    // Ajoute seulement les classes existantes.
    $new_class = null;
    if (isset($_POST['class']) &&
        array_in_array($_POST['class'], $class_list)
    ) {
        $new_class = $_POST['class'];
    }

    try {
        $new_date = trim($_POST['date']);
        $select_date = new DateTime($new_date);
        $today_date  = new DateTime($today);
        $diff = $today_date->diff($select_date);
        if ($diff->invert === 1) {
            $new_date = null;
        }
    } catch (Exception $e) {
        $new_date = null;
    }

    if (empty($new_title) || empty($new_class) || empty($new_date)) {
        $warning = 'Tous les champs doivent être saisi.';
        return;
    }

    // Les informations reçues sont identiques aux informations actuelles.
    if ($new_title === $homework->title &&
        $new_date  === $homework->date &&
        $new_class  === $homework->class
    ) {
        $warning = 'Aucune modification n\'a été apportée';
        return;
    }

    $homework->title = $new_title;
    $homework->date = $new_date;
    $homework->class = $new_class;

    // Modifie le titre dans la base de données.
    if ($homework_db->modify($homework)) {
        $success = 'Le devoir maison à été modifié.';
    }
}
