<?php
/**
 * Base de données des communications.
 *
 * Classe contrôlant les tables des communications.
 *
 * PHP version 7
 */

require_once MODELS_PATH . 'Database.php';
require_once CLASSES_PATH . 'Communication.php';

class CommunicationDatabase extends Database
{
    /**
     * Renvoie les informations concernant une communication.
     *
     * Les informations suivantes sont renvoyées dans un objet `Communication` :
     *  - l'ID de la communication
     *  - l'ID de l'émetteur de la communication
     *  - l'ID du destinataire de la communication
     *  - le message
     *  - l'horodatage de l'envoi
     *
     * @param int $id ID de la communication
     *
     * @return array|bool Objet `Communication`
     */
    public function get(int $id)
    {
        if ($query = $this->pdo->prepare('SELECT * FROM communication 
            WHERE id = ?')
        ) {
            if ($query->execute([$id])) {
                return $query->fetchObject('Communication');
            }
        }
        return false;
    }

    /**
     * Renvoie la liste des communications à partir d'une date.
     *
     * Pour chaque communication les informations suivantes sont renvoyées dans
     * un objet `Communication` :
     *  - l'ID de la communication
     *  - l'ID de l'émetteur de la communication
     *  - l'ID du destinataire de la communication
     *  - le message
     *  - l'horodatage de l'envoi
     *
     * @param int      $page      (optional) Page
     * @param int      $id        (optional) ID de l'utilisteur
     * @param int|null $contact   (optional) ID du contact
     * @param int      $timestamp (optional) Horodatage de l'envoi
     *
     * @return array|null Liste de communications.
     *
     */
    public function get_all(int $page = 1, int $id = null, $contact = null,
        int $timestamp = 0): ?array
    {
        if ($id === null &&
            $query = $this->pdo->prepare('SELECT * FROM communication
                WHERE timestamp > ? ORDER BY timestamp
                LIMIT 10 OFFSET ?')
        ) {
            $query->execute([$timestamp, $page * 10]);
        }
        if (is_int($id) && $contact === null &&
            $query = $this->pdo->prepare('SELECT * FROM communication
                WHERE (sender = ? OR receiver = ?) AND timestamp > ?
                ORDER BY timestamp
                LIMIT 10 OFFSET ?')
        ) {
            $query->execute([$id, $id, $timestamp, $page * 10]);
        }
        if (is_int($id) && is_int($contact) &&
            $query = $this->pdo->prepare('SELECT * FROM communication
                WHERE (sender = ? AND receiver = ?) OR 
                (sender = ? AND receiver = ?) AND timestamp > ?
                ORDER BY timestamp
                LIMIT 10 OFFSET ?')
        ) {
            $query->execute([$id, $contact, $contact, $id, $timestamp, $page * 10]);
        }
        if ($query) {
            return $query->fetchAll(PDO::FETCH_CLASS, 'Communication');
        }
        return null;
    }

    /**
     * Renvoie le nombre de communications.
     *
     * @param int $page      Page
     * @param int $id        ID de l'utilisteur
     * @param int $timestamp Horodatage de l'envoi
     *
     * @return array|null Liste de communications.
     *
     */
    public function count(int $id = null): ?int
    {
        if ($query = $this->pdo->prepare('SELECT COUNT(*) FROM communication
                WHERE sender = ? OR receiver = ?')
        ) {
            $query->execute([$id, $id]);
        }
        if ($query) {
            return $query->fetch(PDO::FETCH_NUM)[0];
        }
        return null;
    }

    /**
     * Ajoute une communication.
     *
     * @param Communication $communication Communication
     *
     * @return bool true si succès, false si erreur
     */
    public function add(Communication $communication): bool
    {
        if ($query = $this->pdo->prepare('INSERT INTO 
            communication(sender, receiver, message, timestamp)
            VALUES (?, ?, ?, ?)')
        ) {
            return $query->execute([
                    $communication->sender,
                    $communication->receiver,
                    $communication->message,
                    $communication->timestamp
                ]
            );
        }
        return false;
    }

    /**
     * Supprime une communication.
     *
     * @param Communication $communication Communication
     *
     * @return bool true si succès, false si erreur
     */
    public function del(Communication $communication): bool
    {
        if ($query = $this->pdo->prepare('DELETE FROM communication
            WHERE id = ?')
        ) {
            return $query->execute([$communication->id]);
        }
        return false;
    }
}
