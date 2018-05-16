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

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getCreated()
    {
        return $this->created;
    }
    // TODO: Setter implementieren
}