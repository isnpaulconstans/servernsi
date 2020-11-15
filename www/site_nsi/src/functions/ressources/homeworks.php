<?php
require_once MODELS_PATH . 'UserDatabase.php';
require_once MODELS_PATH . 'HomeworkDatabase.php';
require_once CLASSES_PATH . 'Homework.php';

function homework_add(HomeworkDatabase $homework_db, string $title,
    string $date, array $file, $class)
{
    $title = htmlentities(trim($title));

    try {
        $date = trim($_POST['date']);
        $select_date = new DateTime($date);
        $today = date('Y-m-d');
        $today_date  = new DateTime($today);
        $diff = $today_date->diff($select_date);
        if ($diff->invert === 1) {
            $date = null;
        }
    } catch (Exception $e) {
        $date = null;
    }

    if (empty($title) || empty($class) || empty($date)) {
        return 'Le titre doit être saisi et une ou plusieurs classes ' .
            'selectionnées';
    }
    // Le fichier est trop lourd.
    if ($_FILES['pdf_file']['size'] > 1000000) {
        return 'Le fichier envoyé est trop volumineux';
    }

    // Le fichier est d'un type autorisé.
    if (!is_accepted($file)) {
        return 'Le format du fichier est incorrect.';
    }

    $homework = new Homework($title, $file['name']);

    do {
        $homework->prod_path = rand_string(10);
    } while (file_exists($homework->prod_path));

    $homework->class = $class;
    $homework->date = $date;

    $new_file = DATA_PATH . 'homework' . DIRECTORY_SEPARATOR .
        $file['name'];
    $prod_path = DATA_PATH . 'homework' . DIRECTORY_SEPARATOR .
        $homework->prod_path;

    // Le fichier existe.
    if (file_exists($new_file)) {
        return 'Le fichier \'' . htmlentities($file['name']) .
            '\' existe déjà';
    }
    if (!mkdir($prod_path)) {
        return 'Erreur lors de la création du répertoire des productions';
    }

    // Déplace le fichier du répertoire temporaire vers le répertoire associé à
    // la ressource.
    if (!move_uploaded_file($file['tmp_name'],
        $new_file)
    ) {
        rmdir($prod_path); // Supprime le répertoire en cas d'erreur.
        return 'Erreur lors de l\'ajout du devoir maison.';
    }

    // Ajoute la nouvelle ressource à la base de données.
    if (!$homework_db->add($homework)) {
        rmdir($prod_path); // Supprime le répertoire en cas d'erreur.
        unlink($file); // Supprime le fichier en cas d'erreur.
        $error = 'Erreur lors de l\'ajout du devoir maison.';
    }
}
