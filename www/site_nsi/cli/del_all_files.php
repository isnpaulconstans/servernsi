#! /usr/bin/php
<?php
define('WEB_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

const DATA_PATH = WEB_ROOT . 'data' . DIRECTORY_SEPARATOR;

// Lecture du fichier de configuration.
define('CONFIG', parse_ini_file(
        WEB_ROOT . 'config' . DIRECTORY_SEPARATOR . 'config.ini',
        true
    )
);

$pdo = new PDO(CONFIG['database']['dsn'],
    CONFIG['database']['user'],
    CONFIG['database']['password']
);

foreach (["course", "activity", "ds", "homework"] as $type) {
    $selection =  "file" . (($type == "homework") ? ", prod_path" : "");
    $query = $pdo->query('SELECT ' . $selection . ' FROM ' . $type);
    $ressources = $query->fetchAll();
    foreach ($ressources as $ressource) {
        $file = DATA_PATH . $type . DIRECTORY_SEPARATOR .
                $ressource["file"];
        if (file_exists($file)) {
            echo "suppression de '" . $file ."'\n";
            unlink($file);
        }
        else {
            echo "Problème avec '" . $file . "'\n";
            exit(1);
        }
        if ($type == "homework") {
            $dir = DATA_PATH . $type . DIRECTORY_SEPARATOR . $ressource["prod_path"];
            echo "suppression du répertoire '" . $dir . "'\n";
            del_dir($dir);
        }
    }
    try {
        $pdo->exec('DELETE FROM ' . $type);
    } catch(PDOException $e) {
        echo "[!] Une erreur est survenue lors de la suppression des " . $type . ".\n";
        exit(1);
    }
    echo "[+] Tous les " . $type . " ont été supprimés.\n";
}
