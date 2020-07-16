<?php
if (empty($_GET['t']) || empty($_GET['f'])) {
    echo '<h2>URL invalide</h2>';
    return;
}

$type = $_GET['t'];
$file = $_GET['f'];

$error = null;
switch ($type) {
    case 'course':
    case 'activity':
        $filename = DATA_PATH . $type . DIRECTORY_SEPARATOR . $file;
        break;
    case 'admin':
        if (!$admin) {
            $filename = null;
            break;
        }
        $filename = DATA_PATH . 'admin' . DIRECTORY_SEPARATOR . $file;
        break;
    case 'homework':
        if (($student) || empty($_GET['id'])) {
            $filename = DATA_PATH . 'homework' . DIRECTORY_SEPARATOR .
                $file;
            break;
        }

        require_once MODELS_PATH . 'HomeworkDatabase.php';
        require_once CLASSES_PATH . 'Homework.php';
        $id = (int)$_GET['id'];

        $homework_db = new HomeworkDatabase;

        if ($homework = $homework_db->get($id)) {
            // Le fichier demandé est lié à un élèves.
            if (isset($homework->returned[$file])) {
                $filename = DATA_PATH . 'homework' .
                    DIRECTORY_SEPARATOR . $homework->prod_path .
                    DIRECTORY_SEPARATOR . $homework->returned[$file];
                break;
            }
        }
        $filename = null;
        break;
    case 'production':
        if ($student) {
            $filename = null;
            break;
        }
        $id = (int)$file;

        require_once MODELS_PATH . 'HomeworkDatabase.php';
        require_once CLASSES_PATH . 'Homework.php';

        $homework_db = new HomeworkDatabase;

        if (!$homework = $homework_db->get($id)) {
            $error = 'Le devoir maison demandé n\'éxiste pas';
            break;
        }
        $dir = DATA_PATH . 'homework' . DIRECTORY_SEPARATOR .
            'production' . DIRECTORY_SEPARATOR;

        // Vide le répertoire `prodution`.
        array_map('unlink', array_filter((array)glob($dir . '*')));

        $zip_file = $dir . $homework->title . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zip_file,
            ZipArchive::CREATE | ZipArchive::OVERWRITE) != true
        ) {
            $error = 'Erreur lors de la création de l\'archive';
            break;
        }

        // Ajoute toutes les productions d'élèves.
        $zip->addGlob(
            DATA_PATH . 'homework' .
                DIRECTORY_SEPARATOR . $homework->prod_path .
                DIRECTORY_SEPARATOR . '*',
            0,
            ['remove_all_path' => true]
        );

        if (!$zip->status === ZIPARCHIVE::ER_OK) {
            $error = 'Erreur lors de la création de l\'archive';
            break;
        }

        $zip->close();
        $filename = $zip_file;
        break;
    default:
        $error = 'Type de fichier inconnu';
}

if ($error || empty($filename)) {
    return;
}

if(!file_exists($filename)) {
    echo '<h2>Fichier introuvable</h2>';
    return;
}

// Indique le type MIME du fichier
header('Content-Type: ' . mime_content_type($filename));
// Indique le nom du fichier
header('Content-Disposition: attachment; filename="' . basename($filename) .
    '"');
// Désactivation du cache
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
// Indique la taille du fichier
header('Content-Length: ' . filesize($filename));

ob_clean();
flush();
readfile($filename);
exit;
