<?php
require_once CLASSES_PATH . 'Ressource.php';
require_once MODELS_PATH . 'RessourceDatabase.php';

function ressource_add(RessourceDatabase $ressource_db, string $ressource_type,
                       string $title, array $file, string $class
) {
    $title = htmlentities(trim($title));
    if (empty($title)) {
        return 'Le titre doit être saisi';
    }
    // Le fichier n'est pas trop lourd.
    if ($file['size'] > 100000000) {
        return 'Le fichier envoyé est trop lourd';
    }

    // Le fichier est d'un type autorisé.
    if (!is_accepted($file)) {
        return 'Le format du fichier est incorrect.';
    }

    $ressource = new Ressource($title, $file['name'], $class);
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
