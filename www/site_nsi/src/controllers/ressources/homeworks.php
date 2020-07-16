<?php
require_once MODELS_PATH . 'UserDatabase.php';
require_once MODELS_PATH . 'HomeworkDatabase.php';
require_once CLASSES_PATH . 'Homework.php';
require_once FUNCTIONS_PATH . 'global.php';
require_once FUNCTIONS_PATH . 'ressources' . DIRECTORY_SEPARATOR .
    'homeworks.php';

$ressource_type = 'homework';
$user_db = new UserDatabase;
$homework_db = new HomeworkDatabase;

// Défini si l'utilisateur peut ou non accéder aux modifications
$allow_edit = $admin || $prof;

$error = null;
$success = null;

$class_list = null;
if ($class_list = $user_db->get_classes()) {
    $class_list = array_slice($class_list, 1);
}

$today = date('Y-m-d');

// Si un fichier a été correctement reçu.
if ($allow_edit &&
    isset($_POST['title']) &&
    isset($_POST['date']) &&
    isset($_FILES['pdf_file']) &&
    $_FILES['pdf_file']['error'] === 0
) {
    // Ajoute les classes seulement si elles existent.
    $class = null;
    if (isset($_POST['class']) &&
        array_in_array($_POST['class'], $class_list)
    ) {
        $class = $_POST['class'];
    }

    // Effectue les vérifications et ajoute le devoir maison.
    $error = homework_add($homework_db, $_POST['title'], $_POST['date'],
        $_FILES['pdf_file'], $class);

    if (empty($error)) {
        $success = 'Le devoir maison a été ajouté.';
    }
}

$week = [
    1 => 'Lundi',
         'Mardi',
         'Mercredi',
         'Jeudi',
         'Vendredi',
         'Samedi',
         'Dimanche'
];
$month = [
    1 => 'Janvier',
         'Février',
         'Mars',
         'Avril',
         'Mai',
         'Juin',
         'Juillet',
         'Août',
         'Septembre',
         'Octobre',
         'Novembre',
         'Décembre'
];

$user = $_SESSION['user'];

$class = $student ? $user->class : null;
$homeworks = $homework_db->get_all($class);

if ($homeworks === null) {
    $error = 'Une erreur est survenue lors de la récupération de la liste ' .
        'des devoirs maisons.';
    return;
}

$homeworks_count = count($homeworks);
foreach ($homeworks as $homework) {
    $students_count = 0;
    // Ajoute le nombre d'élèves dans chaques classes concernées par le devoir
    // maison.
    foreach ($homework->class as $class) {
        $students_count += $user_db->count(STUDENT, $class);
    }
    $homework->students = $students_count;
}
