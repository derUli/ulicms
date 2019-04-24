<?php

use UliCMS\Security\SpamChecker\SpamDetectionResult;

class SpamDetectionResultTest extends PHPUnit\Framework\TestCase {

    public function testConstructor() {
        $result = new SpamDetectionResult("Feld", "Eine Nachricht");
        $this->assertEquals("Feld", $result->field);
        $this->assertEquals("Eine Nachricht", $result->message);
    }

}
