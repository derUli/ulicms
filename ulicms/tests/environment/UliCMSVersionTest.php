<?php

class UliCMSVersionTest extends \PHPUnit\Framework\TestCase {

    public function testGetCodeName() {
        $version = new UliCMSVersion();
        $this->assertNotEmpty($version->getCodeName());
    }

    public function testGetBuildTimestamp() {
        $version = new UliCMSVersion();
        $this->assertIsInt($version->getBuildTimestamp());
    }

    public function testGetBuildDate() {
        $version = new UliCMSVersion();

        $date = $version->getBuildDate();

        $this->assertGreaterThanOrEqual(16, strlen($date));
    }

}
