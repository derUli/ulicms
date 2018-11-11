<?php

namespace UliCMS\Security\SpamChecker;
class SpamDetectionResult
{

    public $field;

    public $message;

    public function __construct($field, $message)
    {
        $this->field = $field;
        $this->message = $message;
    }
}