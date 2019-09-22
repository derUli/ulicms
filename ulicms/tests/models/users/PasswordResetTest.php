<?php

class PasswordResetTest extends \PHPUnit\Framework\TestCase {

    private $testUserId;

    public function setUp() {
        $manager = new UserManager();
        $this->testUserId = intval(
                $manager->getAllUsers()[0]->getId()
        );

        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SERVER_PORT"] = "80";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/";

        $this->cleanUp();
    }

    public function tearDown() {
        $this->cleanUp();
        unset($_SERVER["SERVER_PROTOCOL"]);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['REQUEST_URI']);
    }

    private function cleanUp() {
        Database::truncateTable("password_reset");
    }

    public function testAddToken() {
        $passwordReset = new PasswordReset();
        $this->assertCount(0,
                $passwordReset->getAllTokensByUserId($this->testUserId));

        $token = $passwordReset->addToken($this->testUserId);

        $this->assertEquals(32, strlen($token));
        $this->assertCount(1,
                $passwordReset->getAllTokensByUserId($this->testUserId)
        );

        $this->cleanUp();
    }

    public function testGetPasswordResetLink() {
        $passwordReset = new PasswordReset();
        $token = $passwordReset->addToken($this->testUserId);

        $this->assertEquals(
                "http://example.org/foobar/admin/index.php?sClass=SessionManager" .
                "&sMethod=resetPassword&token={$token}",
                $passwordReset->getPasswordResetLink($token));

        $this->cleanUp();
    }

    public function testGetAllTokensReturnsTokens() {

        $passwordReset = new PasswordReset();
        for ($i = 1; $i < 4; $i++) {
            $passwordReset->addToken(1);
        }
        $passwordReset->addToken($this->testUserId);
        $this->assertCount(4, $passwordReset->getAllTokens());

        $this->cleanUp();
    }

    public function testGetAllTokensReturnsEmptyArray() {
        Database::truncateTable("password_reset");
        $passwordReset = new PasswordReset();
        $this->assertCount(0, $passwordReset->getAllTokens());
    }

    public function testGetAllTokensByUserId() {
        $passwordReset = new PasswordReset();
        for ($i = 1; $i < 4; $i++) {
            $passwordReset->addToken(1);
        }
        $passwordReset->addToken($this->testUserId);
        $this->assertCount(4,
                $passwordReset->getAllTokensByUserId(1));
        $this->assertCount(0,
                $passwordReset->getAllTokensByUserId(PHP_INT_MAX));

        $this->cleanUp();
    }

    public function testGetTokenByTokenString() {
        $passwordReset = new PasswordReset();
        $tokenString1 = $passwordReset->addToken($this->testUserId);
        $tokenString2 = $passwordReset->addToken($this->testUserId);

        $token1 = $passwordReset->getTokenByTokenString($tokenString1);
        $this->assertEquals($tokenString1, $token1->token);
        $this->assertEquals($this->testUserId, $token1->user_id);

        $token2 = $passwordReset->getTokenByTokenString($tokenString2);
        $this->assertEquals($tokenString2, $token2->token);
        $this->assertEquals($this->testUserId, $token2->user_id);

        $this->assertTrue(is_today($token2->date));
        $this->assertTrue(is_today($token1->date));
        $this->cleanUp();
    }

    public function testDeleteToken() {
        $passwordReset = new PasswordReset();

        $token = $passwordReset->addToken($this->testUserId);

        $this->assertNotNull($passwordReset->getTokenByTokenString($token));

        $passwordReset->deleteToken($token);
        $this->assertNull($passwordReset->getTokenByTokenString($token));
    }

}
