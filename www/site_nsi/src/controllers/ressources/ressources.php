<?php
require_once CLASSES_PATH . 'Ressource.php';
require_once MODELS_PATH . 'RessourceDatabase.php';
require_once FUNCTIONS_PATH . 'ressources' . DIRECTORY_SEPARATOR .
    'ressources.php';

// Créer et configure la connexion à la base de données.
$ressource_db = new RessourceDatabase;
$ressource_db->type = $ressource_type;

// Defini si l'utilisateur peut ou non acceder aux modifications
$allow_edit = $admin || $prof;

$error = null;
$success = null;
// Si un fichier a été correctement reçu.
if ($allow_edit &&
    isset($_POST['title']) &&
    isset($_FILES['pdf_file']) &&
    $_FILES['pdf_file']['error'] === 0
) {
    $error = ressource_add($ressource_db, $ressource_type, $_POST['title'],
        $_FILES['pdf_file'], $class);
   if (empty($error)) {
        $success = 'La ressource a été ajouté.';
    }
}

$ressources = $ressource_db->get_all($class);
$ressources_count = count($ressources);

switch ($page_title) {
    case 'activités':
        $ressource_word = 'activité';
        break;
    case 'devoirs surveillés':
        $ressource_word = 'énoncé ou un corrigé';
        break;
    case 'cours':
        $ressource_word = 'cours';
        break;
}
