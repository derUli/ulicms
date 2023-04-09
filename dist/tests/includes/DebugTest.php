<?php

use App\Helpers\TestHelper;

class DebugTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionHandler()
    {
        $output = TestHelper::getOutput(static function() {
            $exception = new Exception('Something is broken');
            exception_handler($exception);
        });

        $this->assertStringContainsString(
            'Exception: Something is broken',
            $output
        );
    }
}
