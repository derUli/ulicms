<?php

use UliCMS\Creators\PlainTextCreator;

class PlainTextCreatorTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $_SERVER["REQUEST_URI"] = "/other-url.html?param=value";
    }

    public function tearDown() {
        unset($_SESSION["language"]);
        unset($_GET["slug"]);
        unset($_SERVER["HTTP_USER_AGENT"]);
        unset($_SERVER["REQUEST_URI"]);
    }

    public function testRender() {
        $_GET["slug"] = "lorem_ipsum";
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; "
                . "Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) "
                . "Chrome/63.0.3239.132 Safari/537.36";
        $expected = normalizeLN(file_get_contents(
                        Path::resolve(
                                "ULICMS_ROOT/tests/fixtures/creators/plain.txt"
                        )
                )
        );
        $creator = new PlainTextCreator();

        $this->assertEquals($expected, $creator->render());
    }

}
