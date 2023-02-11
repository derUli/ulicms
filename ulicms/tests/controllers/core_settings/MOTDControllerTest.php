<?php

class MOTDControllerTest extends \PHPUnit\Framework\TestCase
{
    private $defaultSettings = [];

    protected function setUp(): void
    {
        $this->defaultSettings = [
            "motd" => Settings::get("default_language"),
            "motd_de" => Settings::get("homepage_title_de"),
            "motd_en" => Settings::get("homepage_title_en"),
        ];
    }

    protected function tearDown(): void
    {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePostWith(): void
    {
        $_POST["motd"] = "Hallo Welt!";
        $controller = new MOTDController();
        $controller->_savePost();

        $this->assertEquals(
            "Hallo Welt!",
            Settings::get('motd')
        );
    }

    public function testSavePostWithLanguage(): void
    {
        $_POST["motd"] = "Hallo Welt!";
        $_POST['language'] = "de";
        $controller = new MOTDController();
        $controller->_savePost();

        $this->assertEquals(
            "Hallo Welt!",
            Settings::get('motd_de')
        );
    }
}
