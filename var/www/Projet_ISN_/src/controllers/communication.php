<?php
require_once CLASSES_PATH . 'Communication.php';
require_once MODELS_PATH . 'CommunicationDatabase.php';
require_once CLASSES_PATH . 'User.php';
require_once MODELS_PATH . 'UserDatabase.php';
require_once FUNCTIONS_PATH . 'communication.php';

$user_db          = new UserDatabase;
$communication_db = new CommunicationDatabase;

$user = $_SESSION['user'];

// Crée les listes d'utilisateurs contactable.
if ($student) {
    $student_list = $user_db->get_all(STUDENT, $user->class, $user->id);
} else {
    $student_list = $user_db->get_all(STUDENT);
}
$student_count   = count($student_list);
$professor_list  = $user_db->get_all(PROFESSOR, null, $user->id);
$professor_count = count($professor_list);

$error   = null;
$success = null;

$contact = false;
$contact_id = null;
if (!empty($_GET['id'])) {
    if (!$contact = $user_db->get((int)$_GET['id'])) {
        $error = 'L\'utilisateur n\'éxiste pas.';
        return;
    }
    $contact_id = $contact->id;
}

if ($contact && isset($_POST['message'])) {
    $operation = communication_add($communication_db, $_SESSION['user'],
        $contact, $_POST['message']);
    if ($operation['success']) {
        $success = $operation['message'];
        $page = 0;
    } else {
        $error = $operation['message'];
    }
}

if ($communication_count = $communication_db->count($user->id)) {
    $page_count = ceil($communication_count / 10);
}

if (!isset($page)) {
    $page = 0;
    if (!empty($_GET['page'])) {
        if ($_GET['page'] > 0 && $_GET['page'] < $page_count) {
            $page = $_GET['page'];
        } else {
            $error = 'La page demandée n\'éxiste pas.';
        }
    }
}

$communications = $communication_db->get_all($page, $user->id, $contact_id);
// La structure conditionnelle peut être amélioré.
foreach ($communications as $communication) {
    if ($communication->sender === $user->id) {
        if ($receiver = $user_db->get($communication->receiver)) {
            $communication->receiver = $receiver->last_name . ' ' .
                $receiver->first_name;
        } else {
            $communication->receiver = 'Utilisateur supprimé';
        }
    } else {
        if ($sender = $user_db->get($communication->sender)) {
            $communication->sender = $sender->last_name . ' ' .
                $sender->first_name;
        } else {
            $communication->receiver = 'Utilisateur supprimé';
        }
    }
}

$week = [
    1 => 'Lundi',
         'Mardi',
         'Mercredi',
         'Jeudi',
         'Vendredi',
         'Samedi',
         'Dimanche'
];
$month = [
    1 => 'Janvier',
         'Février',
         'Mars',
         'Avril',
         'Mai',
         'Juin',
         'Juillet',
         'Août',
         'Septembre',
         'Octobre',
         'Novembre',
         'Décembre'
];