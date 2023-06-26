<?php

/**
 * Description of PathTest
 *
 */
class PathTest extends \PHPUnit\Framework\TestCase {
    public function testResolve(): void {
        $this->assertStringEndsWith(
            '/content/log/exception_log/foo.log',
            \App\Utils\Path::resolve('ULICMS_LOG/exception_log/foo.log')
        );
    }
}
