<?php
require_once MODELS_PATH . 'RessourceDatabase.php';
require_once CLASSES_PATH . 'Ressource.php';

$error   = null;
$success = null;
$warning = null;

// Le type ('t') ou l'ID ('id') ne sont pas passés dans l'URI.
if (empty($_GET['t'] || empty($_GET['id']))) {
    $error = 'Requête invalide.';
    return;
}

$type = $_GET['t'];
$id   = (int)$_GET['id'];

$ressource_types = ['course', 'activity'];
if ($id < 0 || !in_array($type, $ressource_types)) {
    $error = '\'' . htmlentities($type) .
        '\' n\'est pas un type de ressource valide.';
    return;
}
    
// Définit le contenu de l'URL après 'ressources/'
if ($type === 'activity') {
    $type_path = 'activities';
} else {
    $type_path = $type . 's';
}

// Créer et configure la connexion à la base de données.
$ressource_db = new RessourceDatabase;
$ressource_db->type = $type;

// La ressource n'existe pas.
if (!$ressource = $ressource_db->get($id)) {
    $error = 'La ressource demandée n\'exite pas.';
    return;
}

// Affiche un message de succès après une redirection.
if (isset($_SESSION['success'])) {
    $success = 'L\'ID a été modifié';
    unset($_SESSION['success']);
}

// Modification
if (!empty($_POST['id']) && !empty($_POST['title'])) {
    $new_title = htmlentities($_POST['title']);
    $new_id    = (int)$_POST['id'];

    // Le titre reçu est différent du titre actuel de la ressource.
    if ($new_title != $ressource->title) {
        $ressource->title = $new_title;
        // Modifie la titre dans la base de données.
        if ($ressource_db->modify($ressource)) {
            $success = 'Le titre du cours à été modifié.';
        }
    }

    // L'ID reçu est différent de l'ID actuel de la ressource.
    if ($new_id != $ressource->id) {
        // Modifie l'ID dans la base de données.
        if (!$ressource_db->change_id($ressource, $new_id)) {
            $warning = 'L\'ID \'' . htmlentities($new_id) . '\' est déjà ' .
                'utilisé.';
            return;
        }

        $_SESSION['success'] = 1;
        // Redirection
        header("Location: /ressources/edit?t=$type&id=$new_id");
        exit;
    }
}

// Suppression
if (!empty($_POST['del'])) {
    // La ressource à supprimer est différente de celle passé en GET.
    if ((int)$_POST['del'] != $id) {
        $error = 'La ressource ne correspond pas.';
        return;
    }

    $ressource_db->pdo->beginTransaction();
    if (!$ressource_db->del($ressource)) {
        // Annule les modifications dans la base de données.
        $ressource_db->pdo->rollBack();
        $error = 'Une erreur est survenue lors de la suppression de la ' .
            'ressource';
        return;
    }

    $file = DATA_PATH . $type . DIRECTORY_SEPARATOR . 
        $ressource->file;

    if (file_exists($file)) {
        unlink($file);
    }

    if (file_exists($file)) {
        // Annule les modifications dans la base de données.
        $ressource_db->pdo->rollBack();
        $error = 'Une erreur est survenue lors de la suppression de la ' .
            'resssource';
        return;
    }

    // Valide les modifications dans la base de données.
    $ressource_db->pdo->commit();

    // Redirection vers la liste des ressources associés.
    header("Location: /ressources/$type_path");
    exit;
}