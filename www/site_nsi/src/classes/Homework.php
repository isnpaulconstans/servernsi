<?php
/**
 * Homework
 *
 * Classe décrivant un devoir maison.
 *
 * PHP version 7
 */

require_once CLASSES_PATH . 'Ressource.php';

class Homework extends Ressource
{
    /** @var string $prod_path Emplacement du repertoire des productions */
    public $prod_path;
    /** @var array $returned Élève(s) ayant rendu le devoir maison */
    public $returned;
    /** @var string $date Date de rendu du devoir maison */
    public $date;
    /** @var int $students Nombre d'élève devant rendre le devoir maison */
    public $students;
}
