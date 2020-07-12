<?php
require_once MODELS_PATH . 'UserDatabase.php';
require_once MODELS_PATH . 'HomeworkDatabase.php';
require_once CLASSES_PATH . 'Homework.php';

$error    = null;
$success  = null;
if (!empty($_GET['id'])) {
    $homework_db = new HomeworkDatabase;
    $id = $_GET['id'];

    if (!$homework = $homework_db->get($id)) {
        $error = 'Le devoir maison demandé n\'existe pas';
        return;
    }

    $user_db = new UserDatabase;
    $students = [];
    // Crée la liste de tout les élèves concernés par un devoir maison.
    foreach ($homework->class as $class) {
        $students = array_merge($students,
                $user_db->get_all(STUDENT, $class)
            );
    }
    $students_returned_count = count($homework->returned);
    $homework->students = count($students);
}