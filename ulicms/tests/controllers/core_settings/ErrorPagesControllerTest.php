<?php

class ErrorPagesControllerTest extends \PHPUnit\Framework\TestCase {

    private $defaultSettings = [];

    protected function setUp(): void {
        $this->defaultSettings = [
            "error_page_403_de" => Settings::get("error_page_403_de"),
            "error_page_403_en" => Settings::get("error_page_403_en"),
            "error_page_404_de" => Settings::get("error_page_404_de"),
            "error_page_404_en" => Settings::get("error_page_404_en")
        ];
    }

    protected function tearDown(): void {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePost(): void {
        $_POST["error_page"] = [
            "403" => [
                "de" => "12",
                "en" => "34"
            ],
            "404" => [
                "de" => "56",
                "en" => "78"
            ]
        ];

        $controller = new ErrorPagesController();
        $controller->_savePost();

        $this->assertEquals(
                "12",
                Settings::get("error_page_403_de")
        );

        $this->assertEquals(
                "34",
                Settings::get("error_page_403_en")
        );

        $this->assertEquals(
                "56",
                Settings::get("error_page_404_de")
        );

        $this->assertEquals(
                "78",
                Settings::get("error_page_404_en")
        );
    }

    public function testSavePostUnset(): void {
        $_POST["error_page"] = [
            "403" => [
                "de" => "12",
                "en" => 0
            ],
            "404" => [
                "de" => "56",
                "en" => 0
            ]
        ];

        $controller = new ErrorPagesController();
        $controller->_savePost();

        $this->assertEquals(
                "12",
                Settings::get("error_page_403_de")
        );
        $this->assertEquals("56", Settings::get("error_page_404_de"));
        $this->assertNull(Settings::get("error_page_404_en"));
        $this->assertNull(Settings::get("error_page_403_en"));
    }

}
