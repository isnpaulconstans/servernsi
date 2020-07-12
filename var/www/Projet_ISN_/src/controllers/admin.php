<?php
require_once CLASSES_PATH . 'User.php';
require_once MODELS_PATH . 'UserDatabase.php';
require_once MODELS_PATH . 'csv.php';
require_once FUNCTIONS_PATH . 'global.php';
require_once FUNCTIONS_PATH . 'admin.php';

$user_db = new UserDatabase;

$error    = null;
$success  = null;
$msg_copy = null;

// Les champs sont tous renseignés.
if (isset($_POST['username']) &&
    isset($_POST['last_name']) &&
    isset($_POST['first_name']) &&
    isset($_POST['role'])
) {
    // Classe de l'utilisateur si défini sinon `null`.
    if (empty($_POST['class'])) {
        $class = null;
    } else {
        $class = htmlentities($_POST['class']);
    }
    
    $operation = user_add($user_db, $_POST['username'], $_POST['last_name'],
        $_POST['first_name'], $_POST['role'], $class);

    if ($operation['success']) {
	$msg_copy = $operation['message'];
	$new_password = $operation['new_password'];
    } else {
        $error = $operation['message'];
    }
}

// Si un fichier a été correctement reçu.
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === 0) {
    $operation = csv_add($user_db, $_FILES['csv_file']);

    if ($operation['success']) {
        $success = $operation['message'];
    } else {
        $error = $operation['message'];
    }
}

// Création des différentes listes nécessaires à l'affichage
$student_list    = $user_db->get_all(STUDENT);
$student_count   = count($student_list);
$professor_list  = $user_db->get_all(PROFESSOR);
$professor_count = count($professor_list);
$admin_list      = $user_db->get_all(ADMIN);
$admin_count     = count($admin_list);

$students_csv = file_exists(DATA_PATH . 'admin' . DIRECTORY_SEPARATOR .
    'students.csv');
