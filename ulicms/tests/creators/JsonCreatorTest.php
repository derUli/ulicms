<?php

use UliCMS\Creators\JSONCreator;

class JsonCreatorTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_SERVER["REQUEST_URI"] = "/other-url.html?param=value";
    }

    public function tearDown() {
        unset($_SESSION["language"]);
        unset($_GET["seite"]);
        unset($_SERVER["HTTP_USER_AGENT"]);
        unset($_SERVER["REQUEST_URI"]);
    }

    public function testRender() {
        $_GET["seite"] = "lorem_ipsum";
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) "
                . "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 "
                . "Safari/537.36";
        $expected = file_get_contents(
                Path::resolve("ULICMS_ROOT/tests/fixtures/creators/json.json")
        );
        $creator = new JSONCreator();

        $this->assertEquals(
                normalizeLN($expected),
                normalizeLN($creator->render())
        );
    }

}
