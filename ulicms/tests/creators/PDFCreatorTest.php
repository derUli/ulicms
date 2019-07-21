<?php

use UliCMS\Creators\PDFCreator;

class PDFCreatorTest extends \PHPUnit\Framework\TestCase {

    public function unset() {
        unset($_SESSION["language"]);
        unset($_GET["seite"]);
        unset($_SERVER["HTTP_USER_AGENT"]);
    }

    public function testRender() {
        $_GET["seite"] = "lorem_ipsum";
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";
        $expected = file_get_contents(
                Path::resolve("ULICMS_ROOT/tests/fixtures/creators/json.json")
        );
        $creator = new PDFCreator();

        $output = $creator->render();
        $this->assertStringStartsWith("%PDF-1.4", $output);
        $this->assertGreaterThanOrEqual(29000,
                getStringLengthInBytes($output));
    }

}
