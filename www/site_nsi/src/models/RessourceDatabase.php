<?php
/**
 * Base de données des ressources.
 *
 * Classe contrôlant les tables des ressources.
 *
 * PHP version 7
 */

require_once MODELS_PATH . 'Database.php';
require_once CLASSES_PATH . 'Ressource.php';

class RessourceDatabase extends Database
{
    /** @var string $ressource_type Type de ressources */
    public $type;

    /**
     * Renvoie les informations concernant une ressource.
     *
     * Les informations suivantes sont renvoyées dans un objet `Ressource` :
     *  - l'ID de la ressource
     *  - le titre de la ressource
     *  - le chemin du fichier de la ressource
     *
     * @param int $id ID de la ressource
     *
     * @return array|bool Objet `Ressource`
     */
    public function get(int $id)
    {
        if ($query = $this->pdo->prepare('SELECT * FROM ' . $this->type .
            ' WHERE id = ?')
        ) {
            if ($query->execute([$id])) {
                return $query->fetchObject('Ressource');
            }
        }
        return false;
    }

    /**
     * Renvoie la liste des ressources de type `type`.
     *
     * Pour chaque ressource les informations suivantes sont renvoyées dans un
     * objet `Ressource` :
     *  - l'ID de la ressource
     *  - le titre de la ressource
     *  - le chemin du fichier
     *
     * @return array|null Liste de ressources.
     *
     */
    public function get_all(string $class): ?array
    {
        if ($query = $this->pdo->query('SELECT * FROM ' . $this->type .
                                       ' WHERE `class` = "' . $class .
                                       '" ORDER BY id'
                                      )
        ) {
            return $query->fetchAll(PDO::FETCH_CLASS, 'Ressource');
        }
        return null;
    }

    /**
     * Ajoute une ressource.
     *
     * @param Ressource $ressource Ressource
     *
     * @return bool true si succès, false si erreur
     */
    public function add(Ressource $ressource): bool
    {
        if ($query = $this->pdo->prepare('INSERT INTO ' . $this->type .
            '(title, file, class) VALUES (?, ?, ?)')
        ) {
            return $query->execute([$ressource->title, $ressource->file,
                                    $ressource->class]);
        }
        return false;
    }

    /**
     * Modifie une ressource.
     *
     * @param Ressource $ressource Ressource
     *
     * @return bool true si succès, false si erreur
     */
    public function modify(Ressource $ressource): bool
    {
        if ($query = $this->pdo->prepare('UPDATE ' . $this->type .
            ' SET title = ?, file = ? WHERE id = ?')
        ) {
            return $query->execute([$ressource->title,
                $ressource->file, $ressource->id]);
        }
        return false;
    }

    /**
     * Modifie l'ID d'une ressource.
     *
     * @param Ressource $ressource Ressource
     * @param int       $new_id    Nouvel ID de la ressource
     *
     * @return bool true si succès, false si erreur
     */
    public function change_id(Ressource $ressource, int $new_id): bool
    {
        if ($query = $this->pdo->prepare('UPDATE ' . $this->type .
            ' SET id = ?  WHERE id = ?')
        ) {
            return $query->execute([$new_id, $ressource->id]);
        }
        return false;
    }

    /**
     * Supprime une ressource.
     *
     * @param Ressource $ressource Ressource
     *
     * @return bool true si succès, false si erreur
     */
    public function del(Ressource $ressource): bool
    {
        if ($query = $this->pdo->prepare('DELETE FROM ' . $this->type .
            ' WHERE id = ?')
        ) {
            return $query->execute([$ressource->id]);
        }
        return false;
    }
}
