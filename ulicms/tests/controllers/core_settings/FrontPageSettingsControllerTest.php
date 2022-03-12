<?php

class FrontPageSettingsControllerTest extends \PHPUnit\Framework\TestCase {

    private $defaultSettings = [];

    protected function setUp(): void {
        $this->defaultSettings = [
            "default_language" => Settings::get("default_language"),
            "frontpage" => Settings::get("frontpage_de"),
            "frontpage_en" => Settings::get("frontpage_en"),
            "frontpag" => Settings::get("meta_description"),
        ];
    }

    protected function tearDown(): void {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePost(): void {
        $_POST["frontpage_de"] = "willkommen";
        $_POST["frontpage_en"] = "welcome";
        Settings::set("default_language", "en");

        $controller = new FrontPageSettingsController();
        $controller->_savePost();

        $this->assertEquals(
                "willkommen",
                Settings::get('frontpage_de')
        );

        $this->assertEquals(
                "welcome",
                Settings::get('frontpage_en')
        );
        $this->assertEquals(
                "welcome",
                Settings::get('frontpage')
        );
    }

}
