<?php

use UliCMS\Security\Encryption;

class EncryptionTest extends \PHPUnit\Framework\TestCase {

    public function testHashPassword() {
        $this->assertEquals(128, strlen(Encryption::hashPassword("foobar")));
        $this->assertEquals(128, strlen(Encryption::hashPassword("hello world")));
        $this->assertEquals(128, strlen(Encryption::hashPassword("topsecret")));
    }

}
