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
        $output = TestHelper::getOutput(function () {
            echo "Hello World!";
        });

        $this->assertEquals("Hello World!", $output);
    }

    public function testGetOutputThrowsException()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Fehler!");

        $output = TestHelper::getOutput(function () {
            throw new BadMethodCallException("Fehler!");
        });

        $this->assertEquals("Hello World!", $output);
    }
}
