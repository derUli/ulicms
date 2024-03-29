<?php

use App\Models\Users\PasswordReset;

class PasswordResetTest extends \PHPUnit\Framework\TestCase {
    private $testUserId;

    protected function setUp(): void {
        $manager = new \App\Models\Users\UserManager();
        $this->testUserId = (int)$manager->getAllUsers()[0]->getId();

        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/';

        $this->cleanUp();
    }

    protected function tearDown(): void {
        $this->cleanUp();
        unset($_SERVER['SERVER_PROTOCOL'], $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $_SERVER['REQUEST_URI']);

    }

    public function testAddToken(): void {
        $passwordReset = new PasswordReset();
        $this->assertCount(
            0,
            $passwordReset->getAllTokensByUserId($this->testUserId)
        );

        $token = $passwordReset->addToken($this->testUserId);

        $this->assertEquals(32, strlen($token));
        $this->assertCount(
            1,
            $passwordReset->getAllTokensByUserId($this->testUserId)
        );

        $passwordReset->sendMail($token, 'john@doe.invalid', '123.123.123.123', 'John', 'Doe');

        $this->cleanUp();
    }

    public function testGetPasswordResetLink(): void {
        $passwordReset = new PasswordReset();
        $token = $passwordReset->addToken($this->testUserId);

        $this->assertEquals(
            'http://example.org/foobar/admin/index.php?sClass=SessionManager' .
            "&sMethod=resetPassword&token={$token}",
            $passwordReset->getPasswordResetLink($token)
        );

        $this->cleanUp();
    }

    public function testGetAllTokensReturnsTokens(): void {
        $passwordReset = new PasswordReset();
        for ($i = 1; $i < 4; $i++) {
            $passwordReset->addToken(1);
        }
        $passwordReset->addToken($this->testUserId);
        $this->assertCount(4, $passwordReset->getAllTokens());

        $this->cleanUp();
    }

    public function testGetAllTokensReturnsEmptyArray(): void {
        Database::truncateTable('password_reset');
        $passwordReset = new PasswordReset();
        $this->assertCount(0, $passwordReset->getAllTokens());
    }

    public function testGetAllTokensByUserId(): void {
        $passwordReset = new PasswordReset();
        for ($i = 1; $i < 4; $i++) {
            $passwordReset->addToken(1);
        }
        $passwordReset->addToken($this->testUserId);
        $this->assertCount(
            4,
            $passwordReset->getAllTokensByUserId(1)
        );
        $this->assertCount(
            0,
            $passwordReset->getAllTokensByUserId(PHP_INT_MAX)
        );

        $this->cleanUp();
    }

    public function testGetTokenByTokenString(): void {
        $passwordReset = new PasswordReset();
        $tokenString1 = $passwordReset->addToken($this->testUserId);
        $tokenString2 = $passwordReset->addToken($this->testUserId);

        $token1 = $passwordReset->getTokenByTokenString($tokenString1);
        $this->assertEquals($tokenString1, $token1->token);
        $this->assertEquals($this->testUserId, $token1->user_id);

        $token2 = $passwordReset->getTokenByTokenString($tokenString2);
        $this->assertEquals($tokenString2, $token2->token);
        $this->assertEquals($this->testUserId, $token2->user_id);

        $this->assertIsString($token2->date);
        $this->assertIsString($token1->date);
        $this->cleanUp();
    }

    public function testDeleteToken(): void {
        $passwordReset = new PasswordReset();

        $token = $passwordReset->addToken($this->testUserId);

        $this->assertNotNull($passwordReset->getTokenByTokenString($token));

        $passwordReset->deleteToken($token);
        $this->assertNull($passwordReset->getTokenByTokenString($token));
    }

    private function cleanUp(): void {
        Database::truncateTable('password_reset');
    }
}
