<?php

/**
 * Description of PathTest
 *
 */
class PathTest extends \PHPUnit\Framework\TestCase {
    public function testNormalize(): void {
        $this->assertStringContainsString(
            DIRECTORY_SEPARATOR,
            Path::normalize('..\foo\bar\file.txt')
        );

        $this->assertStringContainsString(
            DIRECTORY_SEPARATOR,
            Path::normalize('../foo/bar/file.txt')
        );
    }

    public function testResolve(): void {
        $this->assertStringEndsWith(
            '/content/log/exception_log/foo.log',
            Path::resolve('ULICMS_LOG/exception_log/foo.log')
        );
    }
}
