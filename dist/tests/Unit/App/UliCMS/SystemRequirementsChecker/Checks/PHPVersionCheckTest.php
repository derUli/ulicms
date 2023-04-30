<?php
use App\UliCMS\SystemRequirementsChecker\Checks\PHPVersionCheck;
use PHPUnit\Framework\TestCase;

class PHPVersionCheckTest extends TestCase {

    public function testName(): void{
        $check = new PHPVersionCheck();
        $this->assertEquals('php>=8.1', $check->name());
    }

    public function testExpected(): void{
        $check = new PHPVersionCheck();
        $this->assertEquals('>=8.1', $check->expected());
    }

    public function testActual(): void{
        $check = new PHPVersionCheck();
        $this->assertEquals(phpversion(), $check->actual());
    }

    public function testIsFulfilled(): void{
        $check = new PHPVersionCheck();
        $this->assertTrue($check->isFulFilled());
    }
}
