<?php

declare(strict_types=1);

namespace App\Security\SpamChecker;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

// This class is used to show a user if his command was
// detected as spam.
class SpamDetectionResult
{
    public $field;
    public $message;

    public function __construct(string $field, string $message)
    {
        $this->field = $field;
        $this->message = $message;
    }
}
