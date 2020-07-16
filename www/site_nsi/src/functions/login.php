<?php
require_once CLASSES_PATH . 'User.php';
require_once MODELS_PATH . 'UserDatabase.php';

/**
 * Vérifie la validité des identifiants d'un utilisateur.
 *
 * @param UserDatabase $user_db  Base de données des utilisateurs
 * @param string       $username Nom d'utilisateur
 * @param string       $password Mot de passe
 *
 * @return User|string Objet `User` si succès sinon un message d'erreur
 */
function login(UserDatabase $user_db, string $username, string $password)
{
    if (!$user = $user_db->get($username)) {
        return 'Nom d\'utilisateur ou mot de passe incorrect';
    }
    if (!password_verify($password, $user->password_hash)) {
        return 'Nom d\'utilisateur ou mot de passe incorrect';
    }

    return $user;
}