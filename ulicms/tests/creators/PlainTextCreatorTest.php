<?php

use UliCMS\Creators\PlainTextCreator;

class PlainTextCreatorTest extends \PHPUnit\Framework\TestCase {

    private $cacheDisabledOriginal;
    private $cachePeriodOriginal;

    public function setUp() {
        $this->cacheDisabledOriginal = Settings::get("cache_disabled");
        $this->cachePeriodOriginal = Settings::get("cache_period");
        Settings::delete("cache_disabled");
        $_SERVER["REQUEST_URI"] = "/other-url.txt?param=value";
    }

    public function tearDown() {
        if ($this->cacheDisabledOriginal) {
            Settings::set("cache_disabled", "yes");
        } else {
            Settings::delete("cache_disabled");
        }
        Settings::set("cache_period", $this->cachePeriodOriginal);

        unset($_SESSION["language"]);
        unset($_GET["slug"]);
        unset($_SERVER["HTTP_USER_AGENT"]);
        unset($_SERVER["REQUEST_URI"]);
        unset($_SESSION["logged_in"]);
    }

    public function testRender() {
        Settings::delete("cache_disabled");
        Settings::set("cache_period", 500);

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
        $this->assertEquals($expected, $creator->render());
    }

}
