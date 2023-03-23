<?php


class EnvironmentTest extends \PHPUnit\Framework\TestCase
{
    public function testCmsVersion()
    {
        $this->assertTrue(\App\Utils\VersionComparison::compare(
            cms_version(),
            "2019.4",
            ">"
        ));
    }

    public function testGetEnvironment()
    {
        $this->assertEquals("test", get_environment());
    }
}
