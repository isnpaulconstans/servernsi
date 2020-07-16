<?php
require_once MODELS_PATH . 'UserDatabase.php';
require_once CLASSES_PATH . 'User.php';

$user_db = new UserDatabase;

$user = $_SESSION['user'];

$success = null;

// Affiche un message de succès après une redirection.
if (isset($_SESSION['success'])) {
    $success = 'Le mot de passe a été modifié';
    unset($_SESSION['success']);
}

if (isset($_POST['theme'])) {
    $theme = (int)$_POST['theme'];
    if ($theme >= 0 && $theme <= 2) {
        if ($user_db->modify_theme($user->id, $theme)) {
            $user->theme = $theme;
            $success = 'Le thème a été modifié';
            return;
        }
        $error = 'Le thème n\'a pas pu être modifié';
    }
}