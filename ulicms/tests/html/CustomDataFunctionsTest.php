<?php

use UliCMS\Exceptions\NotImplementedException;

class CustomDataFunctionsText extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_GET["slug"] = "welcome";
        $_SESSION["language"] = "en";
    }

    public function tearDown() {
        Settings::delete("my_value");
        delete_custom_data("my_value");
        unset($_GET["slug"]);
        unset($_SESSION["language"]);
    }

    public function testSetAndGetCustomData() {
        $this->assertNull(get_custom_data("hello"));
        set_custom_data("hello", "world");

        $this->assertEquals(
                ["hello" => "world"],
                get_custom_data()
        );

        delete_custom_data();
        $this->assertEquals(
                [], get_custom_data());
    }

    public function testGetReturnsNull() {
        $this->assertNull(
                get_custom_data("gibts_echt_nicht")
        );
    }

}
