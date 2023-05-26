<?php

use App\Helpers\TestHelper;

class TestHelperTest extends \PHPUnit\Framework\TestCase {
    public function testIsRunningPHPUnit(): void {
        $this->assertTrue(TestHelper::isRunningPHPUnit());
    }

    public function testGetOutput(): void {
        $output = TestHelper::getOutput(static function(): void {
            echo 'Hello World!';
        });

        $this->assertEquals('Hello World!', $output);
    }

    public function testGetOutputThrowsException(): void {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Fehler!');

        $output = TestHelper::getOutput(static function(): void {
            throw new BadMethodCallException('Fehler!');
        });

        $this->assertEquals('Hello World!', $output);
    }

    public function testIsWindowsServer(): void {
        $this->assertIsBool(TestHelper::isWindowsServer());
    }
}
