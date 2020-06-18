<?php

class InfoControllerTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        clearCache();
    }

    public function tearDown() {
        clearCache();
    }

    public function testfetchChangelog() {
        $controller = new InfoController();
        $this->assertStringContainsString("Neues in UliCMS 2020",
                $controller->_fetchChangelog());
    }

    public function testGetChangelogInTextarea() {
        $controller = new InfoController();

        $this->assertStringContainsString(
                "Neues in UliCMS 2020",
                $controller->_getChangelogInTextarea()
        );

        $this->assertStringContainsString(
                '<textarea name="changelog" rows="10" cols="80" '
                . 'readonly="readonly">',
                $controller->_getChangelogInTextarea()
        );
    }

    public function testChangelog() {
        $controller = new InfoController();
        $output = $controller->_changelog();
        $this->assertStringContainsString("Neues in UliCMS 2020", $output);
        $this->assertStringContainsString("Neue Features", $output);
    }

    public function testLicense() {
        $controller = new InfoController();
        $output = $controller->_license();
        $this->assertStringContainsString("All rights reserved.", $output);
    }

    public function testGetComposerLegalInfo() {
        $controller = new InfoController();
        $legalInfo = $controller->_getComposerLegalInfo();
        $this->assertStringContainsString(
                "<h1>Project Licenses</h1>",
                $legalInfo
        );

        $legalInfo = $controller->_getComposerLegalInfo();
        $this->assertGreaterThanOrEqual(132000, strlen($legalInfo));
    }

    public function testGetNpmLegalInfo() {
        $controller = new InfoController();
        $npmLegalData = $controller->_getNpmLegalInfo();

        $this->assertCount(15, $npmLegalData);

        foreach ($npmLegalData as $package) {
            $this->assertNotEmpty($package->name);
            $this->assertNotEmpty($package->licenseType);
        }
    }

}
