<?php

use App\Security\Encryption;

class EncryptionTest extends \PHPUnit\Framework\TestCase {

    private $salt;

    protected function setUp(): void {
        $this->salt = Settings::get("password_salt");
    }

    protected function tearDown(): void {
        Settings::set("password_salt", $this->salt);
    }

    public function testHashPassword() {
        $this->assertEquals(128, strlen(Encryption::hashPassword("foobar")));
        $this->assertEquals(128, strlen(Encryption::hashPassword("hello world")));
        $this->assertEquals(128, strlen(Encryption::hashPassword("topsecret")));
    }

    public function testHashPasswordWithCreateNewSalt() {
        Settings::delete("password_salt");

        $this->assertNull(Settings::get("password_salt"));
        $this->assertEquals(128, strlen(Encryption::hashPassword("foobar")));
        $this->assertEquals(128, strlen(Encryption::hashPassword("hello world")));
        $this->assertEquals(128, strlen(Encryption::hashPassword("topsecret")));

        $this->assertEquals(13, strlen("password_salt"));
    }

}
