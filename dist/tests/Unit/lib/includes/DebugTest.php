<?php

use App\Helpers\TestHelper;

class DebugTest extends \PHPUnit\Framework\TestCase {
    public function testExceptionHandler(): void {
        $output = TestHelper::getOutput(static function(): void {
            $exception = new Exception('Something is broken');
            exception_handler($exception);
        });

        $this->assertStringContainsString(
            'Exception: Something is broken',
            $output
        );
    }
}
