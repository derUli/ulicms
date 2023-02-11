<?php

class SiteSloganControllerTest extends \PHPUnit\Framework\TestCase
{
    private $defaultSettings = [];

    protected function setUp(): void
    {
        $this->defaultSettings = [
            "default_language" => Settings::get("default_language"),
            "site_slogan_de" => Settings::get("site_slogan_de"),
            "site_slogan_en" => Settings::get("site_slogan_en"),
            "site_slogan" => Settings::get("site_slogan"),
        ];
    }

    protected function tearDown(): void
    {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePost(): void
    {
        $_POST["site_slogan_de"] = "Lalala und Lelele";
        $_POST["site_slogan_en"] = "Some random stuff";
        Settings::set("default_language", "en");

        $controller = new SiteSloganController();
        $controller->_savePost();

        $this->assertEquals(
            "Lalala und Lelele",
            Settings::get('site_slogan_de')
        );

        $this->assertEquals(
            "Some random stuff",
            Settings::get('site_slogan_en')
        );
        $this->assertEquals(
            "Some random stuff",
            Settings::get('site_slogan')
        );
    }
}
