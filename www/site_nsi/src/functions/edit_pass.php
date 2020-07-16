<?php
require_once CLASSES_PATH . 'User.php';
require_once MODELS_PATH . 'UserDatabase.php';

/**
 * Modifie le mot de passe d'un utilisateur.
 *
 * @param UserDatabase $user_db         Base de données des utilisateurs
 * @param string       $old_pass        Mot de passe actuel de l'utilisateur
 * @param string       $new_pass        Nouveau mot de passe
 * @param string       $confirm_pass    Confirmation du nouveau mot de passe
 * @param int          $password_length Taille minimale du mot de passe
 *
 * @return bool|string `true` si succès sinon un message d'erreur
 */
function edit_pass(UserDatabase $user_db, string $old_pass, string $new_pass,
    string $confirm_pass, int $password_length)
{
    if (empty($old_pass) || empty($new_pass) || empty($confirm_pass)) {
        return 'Tous les champs doivent être renseignés';
    }

    $id = $_SESSION['user']->id;

    if (strlen($new_pass) < $password_length ||
        strlen($confirm_pass) < $password_length
    ) {
        return 'Le mot de passe doit contenir au moins ' . $password_length .
            ' caractères';
    }
    if (!$new_pass === $confirm_pass) {
        return 'Les mots de passe ne correspondent pas';
    }
    if (!$password_hash = $user_db->get_password($id)) {
        return;
    }
    if (!password_verify($old_pass, $password_hash)) {
        return 'Mauvais mot de passe';
    }
    if (!$password = password_hash($new_pass, PASSWORD_DEFAULT)) {
        return 'Erreur fatale !';
    }
    if (!$user_db->modify_password($id, $password)) {
        return 'Une erreur est survenue lors de la modifiation du mot de passe';
    }
    
    return true;
}
