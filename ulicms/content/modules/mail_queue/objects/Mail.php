<?php
namespace MailQueue;

class Mail extends \Model
{

    private $recipient;

    private $headers;

    private $subject;

    private $content;

    private $created;

    public function loadByID($id)
    {}

    public function send()
    {
        throw new NotImplementedException();
    }
    // TODO: Getter und senden implementieren
}