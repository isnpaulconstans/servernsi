<?php
/**
 * Table des devoirs maison
 *
 * Classe contrôlant la table des devoirs maison.
 *
 * PHP version 7
 */

require_once MODELS_PATH . 'Database.php';
require_once CLASSES_PATH . 'Homework.php';

class HomeworkDatabase extends Database
{
    /**
     * Renvoie les informations concernant un devoir maison.
     *
     * Les informations suivantes sont renvoyées dans un objet `Homework` :
     *  - l'ID du devoir maison
     *  - le titre du devoir maison
     *  - le nom du fichier du devoir maison
     *  - le nom du répertoire contenant les productions
     *  - la classe ayant le devoir maison
     *  - la date du devoir maison
     *
     * @param int $id ID du devoir maison
     *
     * @return array|bool Tableau d'objet `Homework`
     */
    public function get(int $id)
    {
        if ($query = $this->pdo->prepare('SELECT * FROM homework WHERE id = ?')
        ) {
            if ($query->execute([$id])) {
                if ($result = $query->fetchObject('Homework')) {
                    $result->class    = json_decode($result->class);
                    $result->returned = json_decode($result->returned, true);
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * Renvoie la liste des devoirs maison.
     *
     * Pour chaque ressource les informations suivantes sont renvoyées dans un
     * objet `Homework` :
     *  - l'ID du devoir maison
     *  - le titre du devoir maison
     *  - le nom du fichier du devoir maison
     *  - le nom du répertoire contenant les productions
     *  - la classe ayant le devoir maison
     *  - la date du devoir maison
     *
     * @param string|null $class Classe
     *
     * @return array|null Liste de devoirs maison ou null si erreur
     *
     */
    public function get_all(string $class = null): ?array
    {
	if ($class) {
            if ($query = $this->pdo->prepare('SELECT * FROM homework 
                WHERE class LIKE ? ORDER BY date')
	    ) {
                if ($query->execute(['%' . $class. '%'])) {
			$result = $query->fetchAll(PDO::FETCH_CLASS, 'Homework');
                }
            }
        } else {
            if ($query = $this->pdo->query('SELECT * FROM homework 
                ORDER BY date, class')
            ) {
                $result = $query->fetchAll(PDO::FETCH_CLASS, 'Homework');
            }
        }
        if (is_array($result)) {
            foreach ($result as &$homework) {
                $homework->class    = json_decode($homework->class);
                $homework->returned = json_decode($homework->returned, true);
            }
            return $result;
        }
        return null;
    }

    /**
     * Ajoute un devoir maison
     *
     * @param Homework $ressource Devoir maison
     *
     * @return bool true si succès, false si erreur
     */
    public function add(Homework $homework): bool
    {
        if ($query = $this->pdo->prepare('INSERT INTO 
            homework(title, file, prod_path, class, returned, date) 
            VALUES (?, ?, ?, ?, \'{}\', ?)')
        ) {
            return $query->execute([
                $homework->title,
                $homework->file,
                $homework->prod_path,
                json_encode($homework->class),
                $homework->date
                ]
            );
        }
        return false;
    }

    /**
     * Ajoute un utilisateur à la liste de ceux qui ont rendu le devoir maison.
     *
     * @param Homework $ressource Devoir maison
     * @param int      $user_id   ID de l'utilisteur
     * @param string   $file      Nom du fichier rendu
     *
     * @return bool true si succès, false si erreur
     */
    public function add_return(Homework $homework, int $user_id,
        string $file): bool
    {
        if ($homework = $this->get($homework->id)) {
            $homework->returned[$user_id] = $file;
            if ($query = $this->pdo->prepare('UPDATE homework
                SET returned = ? WHERE id = ?')
            ) {
                return $query->execute([
                    json_encode($homework->returned),
                    $homework->id
                ]);
            }
        }
        return false;
    }

    /**
     * Supprime un utilisateur à la liste de ceux qui ont rendu le devoir maison.
     *
     * @param Homework $ressource Devoir maison
     * @param string   $username  Nom d'utilisteur
     *
     * @return bool true si succès, false si erreur
     */
    public function del_return(Homework $homework, string $username): bool
    {
        if ($homework = $this->get($homework->id)) {
            unset($homework->returned[$username]);
            if ($query = $this->pdo->prepare('UPDATE homework
                SET returned = ? WHERE id = ?')
            ) {
                return $query->execute([
                    json_encode($homework->returned),
                    $homework->id
                ]);
            }
        }
        return false;
    }

    /**
     * Modifie un devoir maison.
     *
     * @param Homework $ressource Devoir maison
     *
     * @return bool true si succès, false si erreur
     */
    public function modify(Homework $homework): bool
    {
        if ($query = $this->pdo->prepare('UPDATE homework
            SET title = ?, date = ?, class = ? WHERE id = ?')
        ) {
            return $query->execute([$homework->title, $homework->date,
                json_encode($homework->class), $homework->id]);
        }
        return false;
    }

    /**
     * Supprime une ressource.
     *
     * @param Homework $homework Devoir maison
     *
     * @return bool true si succès, false si erreur
     */
    public function del(Homework $homework): bool
    {
        if ($query = $this->pdo->prepare('DELETE FROM homework WHERE id = ?')
        ) {
            return $query->execute([$homework->id]);
        }
        return false;
    }
}
