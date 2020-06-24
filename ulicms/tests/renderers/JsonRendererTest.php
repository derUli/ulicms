<?php

use UliCMS\Renderers\JsonRenderer;
use UliCMS\Utils\CacheUtil;

class JsonRendererTest extends \PHPUnit\Framework\TestCase {

    private $cacheDisabledOriginal;
    private $cachePeriodOriginal;

    protected function setUp(): void {
        require_once getLanguageFilePath("en");
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) "
                . "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 "
                . "Safari/537.36";

        $this->cacheDisabledOriginal = Settings::get("cache_disabled");
        $this->cachePeriodOriginal = Settings::get("cache_period");
        Settings::delete("cache_disabled");
        $_SERVER["REQUEST_URI"] = "/other-url.json?param=value";
        $_SESSION["language"] = "de";
    }

    protected function tearDown(): void {
        CacheUtil::clearPageCache();

        if ($this->cacheDisabledOriginal) {
            Settings::set("cache_disabled", "yes");
        } else {
            Settings::delete("cache_disabled");
        }

        Settings::set("cache_period", $this->cachePeriodOriginal);

        CacheUtil::resetAdapater();

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

        $expected = file_get_contents(
                Path::resolve("ULICMS_ROOT/tests/fixtures/renderers/json.json")
        );
        $renderer = new JsonRenderer();

        $this->assertEquals(
                normalizeLN($expected),
                normalizeLN($renderer->render())
        );

        $this->assertEquals(
                normalizeLN($expected),
                normalizeLN($renderer->render())
        );
    }

    public function testRenderNonExisting() {
        $_GET["slug"] = 'gibts_nicht';

        $renderer = new JsonRenderer();
        $output = $renderer->render();
        $this->assertTrue(is_json($output));
        $this->assertStringContainsString("This page doesn't exist.", $output);
    }

}
