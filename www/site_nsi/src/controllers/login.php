<?php
require_once CLASSES_PATH . 'User.php';
require_once MODELS_PATH . 'UserDatabase.php';
require_once FUNCTIONS_PATH . 'login.php';

$error = null;
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $user_db = new UserDatabase;
    $success = login($user_db, $_POST['username'], $_POST['password']);

    if (is_string($success)) {
        $error = $success;
        return;
    }

    // Connexion de l'utilisateur
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['connected'] = true;
    $_SESSION['user'] = $success;
    // Redirection
    header('Location: /');
    exit();
}
