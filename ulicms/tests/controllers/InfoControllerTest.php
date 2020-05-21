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

    public function testgetChangelogInTextarea() {
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

}
