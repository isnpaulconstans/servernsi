<?php
define('WEB_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

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

try {
    $pdo->exec('DELETE FROM `communication`');
} catch(PDOException $e) {
    echo "[!] Une erreur est survenue lors des communications.\n";
    exit(1);
}

echo "[+] Toutes les communications ont été supprimé.\n";