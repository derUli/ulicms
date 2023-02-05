<?php

class HomepageTitleControllerTest extends \PHPUnit\Framework\TestCase {

    private $defaultSettings = [];

    protected function setUp(): void {
        $this->defaultSettings = [
            "default_language" => Settings::get("default_language"),
            "homepage_title_de" => Settings::get("homepage_title_de"),
            "homepage_title_en" => Settings::get("homepage_title_en"),
            "homepage_title" => Settings::get("homepage_title"),
        ];
    }

    protected function tearDown(): void {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePost(): void {
        $_POST["homepage_title_de"] = "Ulis löbliche Heimseite";
        $_POST["homepage_title_en"] = "Ulis lovely Homepage";
        Settings::set("default_language", "en");

        $controller = new HomepageTitleController();
        $controller->_savePost();

        $this->assertEquals(
                "Ulis löbliche Heimseite",
                Settings::get('homepage_title_de')
        );

        $this->assertEquals(
                "Ulis lovely Homepage",
                Settings::get('homepage_title_en')
        );
        $this->assertEquals(
                "Ulis lovely Homepage",
                Settings::get('homepage_title')
        );
    }

}
