<?php

use UliCMS\Services\Connectors\eXtend\AvailablePackageVersionMatcher;

class AvailablePackageVersionMatcherTest extends \PHPUnit\Framework\TestCase {

    public function testMatchVersionsWithVersionNumber1() {
        $json = file_get_contents_wrapper(
                "https://extend.ulicms.de/ldap_login.json",
                true
        );
        $data = json_decode($json, true);

        $matcher = new AvailablePackageVersionMatcher($data["data"]);
        $compatibleVersions = $matcher->getCompatibleVersions("2019.3");

        $this->assertCount(3, $compatibleVersions);
        $this->assertEquals("2.1", $compatibleVersions[0]["version"]);
        $this->assertEquals("2.0", $compatibleVersions[1]["version"]);
        $this->assertEquals("1.9", $compatibleVersions[2]["version"]);

        foreach ($compatibleVersions as $version) {
            $this->assertTrue(
                    version_compare(
                            $version["compatible_with"],
                            "2017.4",
                            ">="
                    )
            );
        }
    }

    public function testMatchVersionsWithVersionNumber2() {
        $json = file_get_contents_wrapper(
                "https://extend.ulicms.de/mobile_detect_js.json",
                true
        );
        $data = json_decode($json, true);

        $matcher = new AvailablePackageVersionMatcher($data["data"]);

        $compatibleVersions = $matcher->getCompatibleVersions("2016.3");
        $this->assertCount(1, $compatibleVersions);
        $this->assertEquals("1.4.2", $compatibleVersions[0]["version"]);

        $compatibleVersions = $matcher->getCompatibleVersions("2018.3.6");
        $this->assertCount(1, $compatibleVersions);
        $this->assertEquals("1.4.2", $compatibleVersions[0]["version"]);

        $compatibleVersions = $matcher->getCompatibleVersions("2019.3");
        $this->assertCount(0, $compatibleVersions);
    }

    public function testMatchVersionsWithVersionNumber3() {
        $json = file_get_contents_wrapper(
                "https://extend.ulicms.de/ldap_login.json",
                true
        );
        $data = json_decode($json, true);

        $matcher = new AvailablePackageVersionMatcher($data["data"]);
        // future version
        $compatibleVersions = $matcher->getCompatibleVersions("2017.1");

        $this->assertGreaterThanOrEqual(8, count($compatibleVersions));
    }

    public function testMatchVersionsWithoutVersionNumber() {
        $json = file_get_contents_wrapper(
                "https://extend.ulicms.de/oneclick_upgrade.json",
                true
        );
        $data = json_decode($json, true);

        $matcher = new AvailablePackageVersionMatcher($data["data"]);
        $compatibleVersions = $matcher->getCompatibleVersions();
        $this->assertGreaterThanOrEqual(6, count($compatibleVersions));
    }
    
    public function testGetAllVersion(){
        $json = file_get_contents_wrapper(
                "https://extend.ulicms.de/ldap_login.json",
                true
        );
        $data = json_decode($json, true);

        $matcher = new AvailablePackageVersionMatcher($data["data"]);
        $compatibleVersions = $matcher->getAllVersions();

        $this->assertCount(8, $compatibleVersions);
        $this->assertEquals("2.1", $compatibleVersions[0]["version"]);
        $this->assertEquals("1.4", $compatibleVersions[4]["version"]);
        $this->assertEquals("1.1", $compatibleVersions[6]["version"]);
    }

}
