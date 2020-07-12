<?php
/**
 * Communication
 *
 * Classe décrivant une communication.
 *
 * PHP version 7
 */

class Communication
{
    /** @var int $id ID de la communication */
    public $id;
    /** @var int|string $sender ID de l'émetteur | émetteur */
    public $sender;
    /** @var int|srring $receiver ID du destinataire | destinataire */
    public $receiver;
    /** @var string $message Message */
    public $message;
    /** @var int $timestamp Horodatage de l'envoi */
    public $timestamp;

    /**
     * @param int    $sender    ID de l'émetteur
     * @param int    $receiver  ID du destinataire
     * @param string $message   Message
     * @param int    $timestamp Date d'envoi
     * @param int    $id        ID
     *
     * @return void
     */
    public function __construct(
        int    $sender    = null,
        int    $receiver  = null,
        string $message   = null,
        int    $timestamp = null,
        int    $id        = null
        )
    {
        if (!empty($sender)) {
            $this->sender = $sender;
        }
        if (!is_int($this->sender)) {
            $this->sender = (int)$this->sender;
        }
        if (!empty($receiver)) {
            $this->receiver = $receiver;
        }
        if (!is_int($this->receiver)) {
            $this->receiver = (int)$this->receiver;
        }
        if (!empty($message)) {
            $this->message = $message;
        }
        if (!empty($timestamp)) {
            $this->timestamp = $timestamp;
        }
        if (!is_int($this->timestamp)) {
            $this->timestamp = (int)$this->timestamp;
        }
        if (!empty($id)) {
            $this->id = $id;
        }
        if (!is_int($this->id)) {
            $this->id = (int)$this->id;
        }
    }
}
