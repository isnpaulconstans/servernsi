<?php
/**
 * Base de donnée
 *
 * Classe se connectant à la base de données.
 *
 * PHP version 7
 */

/**
 * Décris la base de donnée.
 */
class Database
{
    /** @var PDO $pdo Représente une connexion avec une base de données */
    public $pdo;

    /**
     * Les paramètres par défaut sont ceux du fichier de configuration.
     *
     * @param string $dsn      DSN
     * @param string $user     Utilisateur
     * @param string $password Mot de passe
     *
     * @return void
     */
    public function __construct(
        string $dsn      = CONFIG['database']['dsn'],
        string $user     = CONFIG['database']['user'],
        string $password = CONFIG['database']['password']
    )
    {
        $this->pdo = new PDO($dsn, $user, $password);
    }
}
