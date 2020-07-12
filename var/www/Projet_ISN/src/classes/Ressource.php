<?php
/**
 * Ressource
 *
 * Classe décrivant une ressource (cours, activité, devoir).
 *
 * PHP version 7
 */

class Ressource
{
    /** @var int $id ID de la ressources */
    public $id;
    /** @var string $title Nom du de la ressources */
    public $title;
    /** @var string $file Emplacement du fichier de la ressources */
    public $file;

    /**
     * @param string $title Titre
     * @param string $file  Fichier
     * @param int    $id    ID
     *
     * @return void
     */
    public function __construct(
        string $title   = null,
        string $file    = null,
        int    $id      = null
        )
    {
        if (!empty($title)) {
            $this->title = $title;
        }
        if (!empty($file)) {
            $this->file = $file;
        }
        if (!empty($id)) {
            $this->id = $id;
        }
        if (!is_int($this->id)) {
            $this->id = (int)$this->id;
        }
    }
}
