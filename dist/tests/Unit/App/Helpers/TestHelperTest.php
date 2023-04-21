<?php

use App\Helpers\TestHelper;

class TestHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testIsRunningPHPUnit()
    {
        $this->assertTrue(TestHelper::isRunningPHPUnit());
    }

    public function testGetOutput()
    {
        $output = TestHelper::getOutput(static function() {
            echo 'Hello World!';
        });

        $this->assertEquals('Hello World!', $output);
    }

    public function testGetOutputThrowsException()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Fehler!');

        $output = TestHelper::getOutput(static function() {
            throw new BadMethodCallException('Fehler!');
        });

        $this->assertEquals('Hello World!', $output);
    }
}
