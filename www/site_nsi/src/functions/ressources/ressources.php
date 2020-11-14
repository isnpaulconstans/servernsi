<?php
require_once CLASSES_PATH . 'Ressource.php';
require_once MODELS_PATH . 'RessourceDatabase.php';

function ressource_add(RessourceDatabase $ressource_db, string $ressource_type,
    string $title, array $file
) {
    $title = htmlentities(trim($title));
    if (empty($title)) {
        return 'Le titre doit être saisi';
    }
    // Le fichier n'est pas trop lourd.
    if ($file['size'] > 10000000) {
        return 'Le fichier envoyé est trop lourd';
    }

    $file_info = pathinfo($file['name']);
    // Le fichier est d'un type autorisé.
    if (!in_array($file_info['extension'], CONFIG['production']['extension']) ||
        !in_array($file['type'], CONFIG['production']['mime_type'])
    ) {
        $error = 'Le format du fichier est incorrect.';
        return;
    }
 /*   if ($file_info['extension'] != 'pdf' || $file['type'] != 'application/pdf'
    ) {
        return 'Le fichier envoyé n\'est pas au format PDF';
    }
*/

    $ressource = new Ressource($title, $file['name']);
    $new_file = DATA_PATH . $ressource_type . DIRECTORY_SEPARATOR .
        $file['name'];

    // Le fichier existe.
    if (file_exists($new_file)) {
        return "Le fichier " . $file['name'] . " existe déjà";
    }

    // Déplace le fichier du répertoire temporaire vers le répertoire associé à
    // la ressource.
    if (!move_uploaded_file($file['tmp_name'],
        $new_file)
    ) {
        return 'Erreur lors de l\'enregistrement du fichier';
    }

    // Ajoute la nouvelle ressource à la base de données.
    if (!$ressource_db->add($ressource)) {
        unlink($new_file); // Supprime le fichier en cas d'erreur.
    }
    //var_dump($ressource_db->add($ressource));
}
