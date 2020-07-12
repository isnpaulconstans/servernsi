<?php
/**
 * Utilisateur
 *
 * Classe décrivant un utilisateur.
 *
 * PHP version 7
 */

/**
 * Décrit un utilisateur.
 */
class User
{
    /** @var int $id ID de l'utilisateur */
    public $id;
    /** @var string $username Nom d'utilisateur */
    public $username;
    /** @var int $role Rôle de l'utilisateur */
    public $role;
    /** @var string $last_name Nom de l'utilisateur */
    public $last_name;
    /** @var string $first_name Prénom de l'utilisateur */
    public $first_name;
    /** @var string $class Classe de l'utilisateur */
    public $class;
    /** @var int $theme Thème de l'utilisateur */
    public $theme;
    /** @var string $password Mot de passe de l'utilisateur */
    public $password;
    /** @var string $password_hash Mot de passe hashé de l'utilisateur */
    public $password_hash;
    
    /**
     * @param int    $id            ID de l'utilisateur
     * @param string $username      Nom d'utilisateur
     * @param int    $theme         Rôle
     * @param string $last_name     Prénom
     * @param string $first_name    Nom
     * @param string $class         Classe
     * @param int    $theme         Thème
     * @param string $password      Mot de passe
     * @param string $password_hash Mot de passe hashé
     *
     * @return void
     */
    public function __construct(
        int    $id            = null,
        string $username      = null,
        int    $role          = null,
        string $last_name     = null,
        string $first_name    = null,
        string $class         = null,
        int    $theme         = null,
        string $password      = null,
        string $password_hash = null
    ) {
        if (!empty($id)) {
            $this->id = $id;
        }
        if (!is_int($this->id)) {
            $this->id = (int)$this->id;
        }
        if (!empty($username)) {
            $this->username = $username;
        }
        if (!empty($role)) {
            $this->role = $role;
        }
        if (!is_int($this->role)) {
            $this->role = (int)$this->role;
        }
        if (!empty($last_name)) {
            $this->last_name = $last_name;
        }
        if (!empty($first_name)) {
            $this->first_name = $first_name;
        }
        if (!empty($class)) {
            $this->class = $class;
        }
        if (!empty($theme)) {
            $this->theme = $theme;
        }
        if (!is_int($this->theme)) {
            $this->theme = (int)$this->theme;
        }
        if (!empty($password)) {
            $this->password = $password;
        }
        if (!empty($password_hash)) {
            $this->password_hash = $password_hash;
        }
    }
}