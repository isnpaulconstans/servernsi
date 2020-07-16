<?php
require_once CLASSES_PATH . 'User.php';
require_once MODELS_PATH . 'UserDatabase.php';
require_once FUNCTIONS_PATH . 'edit_pass.php';

$password_length = (int)CONFIG['password']['min_length'];
$error = null;
if (isset($_POST['old_pass']) &&
    isset($_POST['new_pass']) &&
    isset($_POST['confirm_pass'])
) {
    $user_db = new UserDatabase;
    $success = edit_pass($user_db, $_POST['old_pass'], $_POST['new_pass'],
        $_POST['confirm_pass'], $password_length);

    if ($success === true) {
        $_SESSION['success'] = 1;
        // Redirection
        header('Location: /profil');
        exit();
    }
    $error = $success;
}
