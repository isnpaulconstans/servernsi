<?php
define('WEB_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Lecture du fichier de configuration.
define('CONFIG', parse_ini_file(
        WEB_ROOT . 'config' . DIRECTORY_SEPARATOR . 'config.ini',
        true
    )
);

// Fuseau horaire.
// sed -i -e "s/;date.timezone =/date.timezone = Europe\/Paris/" "/etc/php/7.2/fpm/php.ini"

if(!function_exists('mime_content_type')) {
    echo "[!] L\'extension 'fileinfo' doit être activée dans le fichier 'php.ini'.";
    exit(1);
}

$pdo = new PDO(CONFIG['database']['dsn'],
    CONFIG['database']['user'],
    CONFIG['database']['password']
);

try {
    $pdo->exec(
        'CREATE TABLE `user` (
            `id`            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
            `username`      VARCHAR ( 255 ) NOT NULL UNIQUE,
            `password_hash` VARCHAR ( 255 ) NOT NULL,
            `role`          INTEGER NOT NULL,
            `last_name`     VARCHAR ( 127 ) NOT NULL,
            `first_name`    VARCHAR ( 127 ) NOT NULL,
            `class`         VARCHAR ( 15 ),
            `theme`         INTEGER NOT NULL
        );'
    );
    $pdo->exec(
        'INSERT INTO user(username, password_hash, role, last_name, first_name, theme)
        VALUES (\'admin\', \'$2y$10$t1udBJ/lpW6X765.dDlSueEmEx/7NVjHNNZGEL.MqDMc1fm4VLCwq\', 2, \'Administrateur\', \'\', 0)'
    );
    $pdo->exec(
        'CREATE TABLE `communication` (
            `id`        INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
            `sender`    INTEGER NOT NULL,
            `receiver`  INTEGER NOT NULL,
            `message`   VARCHAR ( 1023 ) NOT NULL,
            `timestamp` INT NOT NULL
        );'
    );
    $pdo->exec(
        'CREATE TABLE `course` (
        	`id`	INTEGER NOT NULL PRIMARY KEY UNIQUE,
        	`title`	VARCHAR ( 255 ) NOT NULL,
        	`file`	VARCHAR ( 255 ) NOT NULL,
            `class` VARCHAR ( 255 ) NOT NULL
        );'
    );
    $pdo->exec(
        'CREATE TABLE `activity` (
        	`id`	INTEGER NOT NULL PRIMARY KEY UNIQUE,
        	`title`	VARCHAR ( 255 ) NOT NULL,
        	`file`	VARCHAR ( 255 ) NOT NULL,
            `class` VARCHAR ( 255 ) NOT NULL
        );'
    );
    $pdo->exec(
        'CREATE TABLE `homework` (
        	`id`	    INTEGER NOT NULL PRIMARY KEY UNIQUE,
        	`title`	    VARCHAR ( 255 ) NOT NULL,
        	`file`	    VARCHAR ( 255 ) NOT NULL,
            `prod_path` VARCHAR ( 255 ) NOT NULL,
            `class`     VARCHAR ( 255 ) NOT NULL,
            `returned`  VARCHAR ( 255 ) NOT NULL,
            `date`      DATE NOT NULL
        );'
    );
} catch(PDOException $e) {
    echo '[!] Une erreur est survenue lors de l\'initialisation de la base ' .
        "de données.\n";
    exit(1);
}

echo "[+] La base de données a été initialisée.\n";

$dirs = [
    'data/activity',
    'data/admin',
    'data/course',
    'data/homework/production'
];
foreach ($dirs as $dir) {
    if (!is_dir(WEB_ROOT . $dir)) {
        if (!mkdir(WEB_ROOT . $dir, 0770, true)) {
            echo '[!] Une erreur est survenue lors de la création des ' .
                "répertoires de données.\n";
            exit(1);
        }
    }
}

echo "[+] Les répertoires de données ont été créés.\n";
