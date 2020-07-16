<?php
require_once CLASSES_PATH . 'User.php';
require_once CLASSES_PATH . 'Communication.php';
require_once MODELS_PATH . 'CommunicationDatabase.php';
require_once FUNCTIONS_PATH . 'global.php';


/**
 * Vérifie les données et ajoute une communication à la base de données.
 *
 * @param CommunicationDatabase $communication_db Base de données des communications
 * @param User                  $user             Utilisateur émetteur
 * @param User                  $contact          Utilisateur destinataire
 * @param string                $message          Message
 *
 * @return array Tableau indiquant le succès ou non et le message 
 */
function communication_add(CommunicationDatabase $communication_db, User $user,
    User $contact, string $message
) {
    $message = trim($message);

    if (strlen($message) < 2) {
        return [
            'success' => false,
            'message' => 'Le message doit contenir au moins 2 caractères.'
        ];
    }

    if ($user->id === $contact->id) {
        return [
            'success' => false,
            'message' => 'Vous ne pouvez pas communiquer avec vous-même'
        ];
    }

    if (($user->role === STUDENT && $contact->role === STUDENT &&
        $contact->class != $user->class) || $contact->role === ADMIN
    ) {
        return [
            'success' => false,
            'message' => 'Vous n\'avez pas l\'autorisation de communiquer ' .
            'avec cet utilisateur.'
        ];
    }

    $communication = new Communication(
        $user->id,
        $contact->id,
        $message,
        time()
    );

    if (!$communication_db->add($communication)) {
        return [
            'success' => false,
            'message' => 'Échec de l\'envoi du message.'
        ];
    }

    return [
        'success' => true,
        'message' => 'Message envoyé à ' . $contact->last_name . ' ' .
        $contact->first_name . '.'
    ];
}