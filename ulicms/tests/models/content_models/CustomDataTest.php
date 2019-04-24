<?php

require_once ULICMS_ROOT . "/templating.php";

class CustomDataTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_GET["seite"] = "welcome";
        $_SESSION["language"] = "en";
    }

    public function tearDown() {
        Settings::delete("my_value");
        CustomData::delete("my_value");
        unset($_GET["seite"]);
        unset($_SESSION["language"]);
    }

    public function testGetCustomDataOrSetting() {
        Settings::set("my_value", "text1");
        $this->assertEquals("text1", CustomData::getCustomDataOrSetting("my_value"));
        CustomData::set("my_value", "text2");
        $this->assertEquals("text2", CustomData::getCustomDataOrSetting("my_value"));
        CustomData::delete("my_value");
        $this->assertEquals("text1", CustomData::getCustomDataOrSetting("my_value"));
    }

}
