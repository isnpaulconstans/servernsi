<?php
/**
 * Table des utilisateurs
 *
 * Classe contrôlant la table des utilisateurs.
 *
 * PHP version 7
 */

require_once MODELS_PATH . 'Database.php';
require_once CLASSES_PATH . 'User.php';

class UserDatabase extends Database
{
    /**
     * Vérifie si un utilisateur existe.
     *
     * @param string $username Nom d'utilisateur
     *
     * @return bool true si l'utilisateur existe sinon false
     */
    public function exist(string $username): bool
    {
        $query = $this->pdo->prepare('SELECT username FROM user WHERE
            username = ?');
        if ($query->execute([$username])) {
            return strlen($query->fetchColumn()) > 0;
        }
    }

    /**
     * Renvoie les informations concernant un utilisateur.
     *
     * Les informations sont les suivantes :
     *  - l'ID
     *  - le nom d'utilisateur
     *  - le role
     *  - le nom
     *  - le prénom
     *  - la classe
     *  - le thème
     *
     * @param string|int $identifier Nom d'utilisateur
     *
     * @return User|bool Informations
     */
    public function get($identifier)
    {
        if (is_int($identifier)) {
            $query = $this->pdo->prepare('SELECT * FROM user 
                WHERE id = ?');
        } else {
            $query = $this->pdo->prepare('SELECT * FROM user 
                WHERE username = ?');
        }
        if ($query->execute([$identifier])) {
            return $query->fetchObject('User');
        }
        return false;
    }

    /**
     * Renvoie le mot de passe d'un utilisateur.
     *
     * @param int $id ID de l'utilisateur
     *
     * @return string|bool Mot de passe ou false si erreur
     */
    public function get_password(int $id)
    {
        if ($query = $this->pdo->prepare('SELECT password_hash FROM user
            WHERE id = ?')
        ) {
            if ($query->execute([$id])) {
                return $query->fetch(PDO::FETCH_ASSOC)['password_hash'];
            }
        }
        return false;
    }

    /**
     * Renvoie la liste des utilisateurs.
     *
     * Il est possible de sélectionner un type d'utilisateur en paramètre.
     * Pour chaque utilisateur les informations suivantes sont renvoyées :
     *  - l'ID
     *  - le nom d'utilisateur
     *  - le role
     *  - le nom
     *  - le prénom
     *  - la classe
     *
     * @param int|null    $role  (optional) Rôle d'utilisateurs
     * @param string|null $class (optional) Classe d'utilisateurs
     * @param int|null    $diff  (optional) ID de l'utilisateur à exclure
     *
     * @return array|null Liste d'utilisateur
     */
    public function get_all(int $role = null, string $class = null,
        int $diff = -1): ?array
    {
        if ($role === null && $class === null) {
            if ($query = $this->pdo->query('
                SELECT id, username, role, last_name, first_name, class 
                FROM user
                ORDER BY role, class, last_name, first_name')
            ) {
                return $query->fetchAll(PDO::FETCH_CLASS, 'User');
            }
        }
        if (is_int($role) && $class === null) {
            if ($query = $this->pdo->prepare('
                SELECT id, username, role, last_name, first_name, class 
                FROM user 
                WHERE role = ? AND id != ?
                ORDER BY class, last_name, first_name')
            ) {
                if ($query->execute([$role, $diff])) {
                    return $query->fetchAll(PDO::FETCH_CLASS, 'User');
                }
            }
        }
        if (is_int($role) && is_string($class)) {
            if ($query = $this->pdo->prepare('
                SELECT id, username, role, last_name, first_name, class 
                FROM user 
                WHERE role = ? AND class = ? AND id != ?
                ORDER BY last_name, first_name')
            ) {
                if ($query->execute([$role, $class, $diff])) {
                    return $query->fetchAll(PDO::FETCH_CLASS, 'User');
                }
            }
        }
        return null;
    }

    /**
     * Renvoie la liste des utilisateurs.
     *
     * Il est possible de sélectionner un type d'utilisateur en paramètre.
     * Pour chaque utilisateur les informations suivantes sont renvoyées :
     *  - l'ID
     *  - le nom d'utilisateur
     *  - le role
     *  - le nom
     *  - le prénom
     *  - la classe
     *
     * @param int|null    $role  (optional) Rôle des utilisateurs
     * @param string|null $class (optional) Classe des utilisateurs
     *
     * @return int|null Nombre d'utilisateurs
     */
    public function count(int $role = null, string $class = null): ?int
    {
        if ($role === null && $class === null) {
            if ($query = $this->pdo->query('SELECT COUNT(*) FROM user')
            ) {
                return (int)$query->fetch(PDO::FETCH_NUM)[0];
            }
        }
        if (is_int($role) && $class === null) {
            if ($query = $this->pdo->prepare('SELECT COUNT(*) FROM user
                WHERE role = ?')
            ) {
                if ($query->execute([$role])) {
                    return (int)$query->fetch(PDO::FETCH_NUM)[0];
                }
            }
        }
        if (is_int($role) && is_string($class)) {
            if ($query = $this->pdo->prepare('SELECT COUNT(*) FROM user
                WHERE role = ? AND class = ?')
            ) {
                if ($query->execute([$role, $class])) {
                    return (int)$query->fetch(PDO::FETCH_NUM)[0];
                }
            }
        }
        return null;
    }

    /**
     * Renvoie toutes les classes.
     *
     * @return array|bool Liste de classes
     */
    public function get_classes(): ?array
    {
        if ($query = $this->pdo->query('SELECT DISTINCT class FROM user')) {
            return $query->fetchAll(PDO::FETCH_COLUMN);
        }
        return null;
    }

    /**
     * Ajoute un utilisateur.
     *
     * @param User $user Utilisateur
     *
     * @return bool true si succès, false si erreur
     */
    public function add(User $user): bool
    {
        if ($query = $this->pdo->prepare('INSERT INTO user(username, 
            password_hash, role, last_name, first_name, class, theme) VALUES 
            (?, ?, ?, ?, ?, ?, 0)')
        ) {
            return $query->execute([
                    $user->username,
                    $user->password_hash,
                    $user->role,
                    $user->last_name,
                    $user->first_name,
                    $user->class
                ]
            );
        }
        return false;
    }

    /**
     * Modifie un utilisateur.
     *
     * @param string      $username     Nom d'utilisateur
     * @param string      $last_name    Nom
     * @param string      $first_name   Prénom
     * @param string      $new_username Nouveau nom d'utilisateur
     * @param string|null $class        (optional) Classe
     *
     * @return bool true si succès, false si erreur
     */
    public function modify(string $username, string $last_name,
        string $first_name, string $new_username, string $class = null
    ): bool
    {
        if ($query = $this->pdo->prepare('UPDATE user SET username = ?,
            last_name = ?, first_name = ?, class = ? WHERE username = ?')
        ) {
            return $query->execute([$new_username, $last_name, $first_name,
                $class, $username]
            );
        }
        return false;
    }

    /**
     * Modifie le mot de passe d'un utilisateur.
     *
     * @param int    $id            ID de l'utilisateur
     * @param string $password_hash Hash du mot de passe
     *
     * @return bool true si succès, false si erreur
     */
    public function modify_password(int $id, string $password_hash
    ): bool
    {
        if ($query = $this->pdo->prepare('UPDATE user SET password_hash = ?
            WHERE id = ?')
        ) {
            return $query->execute([$password_hash, $id]);
        }
        return false;
    }

    /**
     * Modifie le thème d'un utilisateur.
     *
     * @param int $id    ID de l'utilisateur
     * @param int $theme Thème
     *
     * @return bool true si succès, false si erreur
     */
    public function modify_theme(string $id, int $theme): bool
    {
        if ($query = $this->pdo->prepare('UPDATE user SET theme = ?
            WHERE id = ?')
        ) {
            return $query->execute([$theme, $id]);
        }
        return false;
    }

    /**
     * Supprime un utilisateur.
     *
     * @param int $id ID de l'utilisateur
     *
     * @return bool true si succès, false si erreur
     */
    public function del(int $id): bool
    {
        if ($query = $this->pdo->prepare('DELETE FROM user WHERE id = ?')
        ) {
            return $query->execute([$id]);
        }
        return false;
    }
}