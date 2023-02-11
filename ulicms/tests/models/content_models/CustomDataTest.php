<?php

class CustomDataTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $_GET["slug"] = "welcome";
        $_SESSION['language'] = "en";
    }

    protected function tearDown(): void
    {
        CustomData::delete("my_value");
        unset($_GET["slug"]);
        unset($_SESSION['language']);
    }

    public function testGetCustomDataOrSetting()
    {
        Settings::set("my_value", "text1");
        $this->assertEquals("text1", CustomData::getCustomDataOrSetting("my_value"));
        CustomData::set("my_value", "text2");
        $this->assertEquals("text2", CustomData::getCustomDataOrSetting("my_value"));
        CustomData::delete("my_value");
        $this->assertEquals("text1", CustomData::getCustomDataOrSetting("my_value"));
    }

    public function testGetReturnsEmpty()
    {
        $this->assertEquals(
            [],
            CustomData::get("gibts_echt_nicht")
        );
    }

    public function testGetDefaultJSON()
    {
        $json = CustomData::getDefaultJSON();
        $this->assertNotEmpty($json);
        $this->assertTrue(is_json($json));
    }

    public function testGetDefaultReturnsEmptyArray()
    {
        $this->assertNull(CustomData::getDefault("unknown_type"));
    }

    public function testGetDefaultReturnsArray()
    {
        Customdata::setDefault("some_data", "123");
        $this->assertEquals(
            "123",
            CustomData::getDefault("some_data")
        );
    }
}
