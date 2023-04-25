<?php

class EnvironmentTest extends \PHPUnit\Framework\TestCase {
    public function testCmsVersion(): void {
        $this->assertTrue(\App\Utils\VersionComparison::compare(
            cms_version(),
            '2019.4',
            '>'
        ));
    }

    public function testGetEnvironment(): void {
        $this->assertEquals('test', get_environment());
    }
}
