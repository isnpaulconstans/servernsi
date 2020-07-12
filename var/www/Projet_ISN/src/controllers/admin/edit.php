<?php
require_once MODELS_PATH . 'UserDatabase.php';
require_once CLASSES_PATH . 'User.php';

$user_db = new UserDatabase;

$error    = null;
$success  = null;
$warning  = null;
$msg_copy = null;

if (empty($_GET['id'])) {
    $error = "Requête invalide";
    return;
}

$id = (int)$_GET['id'];
// L'utilisateur est invalide.
if (!$user = $user_db->get($id)) {
    $error = "<strong>$id</strong> n'est pas un ID d'utilisateur valide.";
    return;
}

// Les informations de l'utilisateur sont éditées.
if (isset($_POST['last_name']) &&
    isset($_POST['first_name']) &&
    isset($_POST['username']) &&
    ($user->role != STUDENT || isset($_POST['class']))
) {
    $last_name    = htmlentities(trim($_POST['last_name']));
    $first_name   = htmlentities(trim($_POST['first_name']));
    if ($user->role === STUDENT) {
        $class = htmlentities(trim($_POST['class']));
    } else {
        $class = null;
    }
    $new_username = htmlentities(trim($_POST['username']));

    if (empty($new_username) || empty($last_name) || empty($first_name) ||
        ($user->role === STUDENT && empty($class))
    ) {
        $warning = 'Tous les champs doivent être remplis.';
        return;
    }

    if ($new_username != $user->username && $user_db->exist($new_username)) {
        $warning = 'Le nom de l\'utilisateur existe déjà.';
        return;
    }

    if ($user_db->modify(
            $user->username,
            $last_name,
            $first_name,
            $new_username,
            $class
        ) &&
        $user = $user_db->get($id)
    ) {
        $success = 'L\'utilisateur a été modifié.';
    }
}

// Régénération du mot de passe.
if (!empty($_POST['regen_id']) && (int)$_POST['regen_id'] === $id) {
    $new_password = empty($_POST['new_password']) ?
	  rand_string(CONFIG['password']['generated_length'])
	: htmlentities(trim($_POST['new_password']));
    if (!$password = password_hash($new_password, PASSWORD_DEFAULT)) {
        $warning = 'La régération d\'un nouveau mot de passe a échoué.';
    }

    // Le mot de passe a correctement été modifié.
    if ($user_db->modify_password($id, $password) &&
        $user = $user_db->get($id)
    ) {
        $msg_copy = 'Le nouveau mot de passe de <strong> ' . $user->last_name .
            ' ' . $user->first_name . '</strong> (' . $user->username .
            ') est : <strong>' . $new_password . '</strong>';
    }
}

// Suppression de l'utilisateur.
if ((!empty($_POST['del_id']) &&
    (int)$_POST['del_id'] === $id)
) {
    if (!$user_db->del($id)) {
        $warning = 'Échec de la suppression de l\'utilisateur';
        return;
    }
    
    // Redirection.
    $_SESSION['success'] = 1;
    header('Location: /admin');
    exit();
}
