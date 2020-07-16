<?php
/**
 * Fonctions
 *
 * Défini les fonctions.
 *
 * PHP version 7
 */

ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.name', '__Secure-NSISESSIONID');


require_once CLASSES_PATH . 'User.php';


/**
 * Vérifie si l'utilisateur actuel est connecté.
 *
 * @return bool true si l'utilisateur est connecté sinon false
 */
function is_connected(): bool
{
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
    }
    return !empty($_SESSION['connected']);
}

/**
 * Vérifie si l'utilisateur actuel est un administrateur.
 *
 * @return bool true si l'utilisateur est un administrateur sinon false
 */
function is_admin(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user']->role === ADMIN;
}

/**
 * Vérifie si l'utilisateur actuel est un professeur.
 *
 * @return bool true si l'utilisateur est un professeur sinon false
 */
function is_prof(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user']->role === PROFESSOR;
}

/**
 * Vérifie si l'utilisateur actuel est un élève.
 *
 * @return bool true si l'utilisateur est un élève sinon false
 */
function is_student(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user']->role === STUDENT;
}

/**
 * Génère une chaine de caractères aléatoires.
 *
 * @param int $length Taille de la chaine de caractères (62 max)
 *
 * @return string Chaine de caractères générée.
 */
function rand_string(int $length): string
{

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $length);

}

/**
 * Indique si tous les éléments d'un tableau appartiennent à un second tableau.
 *
 * @param array $needles  Tableau d'éléments recherchés
 * @param array $haystack Tableau
 *
 * @return bool true si tous les éléments sont dans le second tableau sinon false
 */
function array_in_array(array $needles, array $haystack)
{
    foreach ($needles as $needle) {
        if (!in_array($needle, $haystack)) {
            return false;
        }
    }
    return true;
}

/**
 * Supprime un fichier ou un répertoire récusivement.
 *
 * @param string $target Fichier ou répertoire à supprimer
 *
 * @return void
 */
function del_dir($target) {
    if (is_dir($target)) {
        $files = glob($target . '*', GLOB_MARK);

        foreach ($files as $file){
            del_dir($file);      
        }

        rmdir( $target );
    } elseif (is_file($target)) {
        unlink($target);  
    }
}
