<?php

class MetaDescriptionControllerTest extends \PHPUnit\Framework\TestCase
{
    private $defaultSettings = [];

    protected function setUp(): void
    {
        $this->defaultSettings = [
            "default_language" => Settings::get("default_language"),
            "meta_description_de" => Settings::get("meta_description_de"),
            "meta_description_en" => Settings::get("meta_description_en"),
            "meta_description" => Settings::get("meta_description"),
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
        $_POST["meta_description_de"] = "Die Meta Beschreibung";
        $_POST["meta_description_en"] = "The Meta Description";
        Settings::set("default_language", 'en');

        $controller = new MetaDescriptionController();
        $controller->_savePost();

        $this->assertEquals(
            "Die Meta Beschreibung",
            Settings::get('meta_description_de')
        );

        $this->assertEquals(
            "The Meta Description",
            Settings::get('meta_description_en')
        );
        $this->assertEquals(
            "The Meta Description",
            Settings::get('meta_description')
        );
    }
}
