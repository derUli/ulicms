<?php

use UliCMS\Creators\PlainTextCreator;
use UliCMS\Utils\CacheUtil;

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
        CacheUtil::clearPageCache();
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);

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
        Settings::set("cache_period", "500");
        CacheUtil::resetAdapater();
        
        $_GET["slug"] = "lorem_ipsum";
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; "
                . "Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) "
                . "Chrome/63.0.3239.132 Safari/537.36";
        $expected = normalizeLN(
                file_get_contents(
                        Path::resolve(
                                "ULICMS_ROOT/tests/fixtures/creators/plain.txt"
                        )
                )
        );
        $creator = new PlainTextCreator();

        $this->assertEquals($expected, normalizeLN($creator->render()));
        $this->assertEquals($expected, normalizeLN($creator->render()));
    }

    public function testRenderWithTextPositionBefore() {
        $modulePage = new Module_Page();
        $modulePage->title = "Unit Test Article";
        $modulePage->slug = "unit-test-" . uniqid();
        $modulePage->menu = "none";
        $modulePage->language = "de";
        $modulePage->article_date = 1413821696;
        $modulePage->author_id = 1;
        $modulePage->group_id = 1;
        $modulePage->module = 'fortune2';
        $modulePage->text_position = 'before';
        $modulePage->save();

        $_SERVER["REQUEST_URI"] = "/one-url.txt?param=value";
        Settings::delete("cache_disabled");
        Settings::set("cache_period", 500);

        $_GET["slug"] = "lorem_ipsum";

        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; "
                . "Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) "
                . "Chrome/63.0.3239.132 Safari/537.36";

        $creator = new PlainTextCreator();

        $output = $creator->render();
        $output = $creator->render();

        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    public function testRenderWithTextPositionAfter() {
        $_SERVER["REQUEST_URI"] = "/url-two.txt?param=value&foo=bar";

        $modulePage = new Module_Page();
        $modulePage->title = "Unit Test Article";
        $modulePage->slug = "unit-test-" . uniqid();
        $modulePage->menu = "none";
        $modulePage->language = "de";
        $modulePage->article_date = 1413821696;
        $modulePage->author_id = 1;
        $modulePage->group_id = 1;
        $modulePage->module = 'fortune2';
        $modulePage->text_position = 'after';
        $modulePage->save();

        Settings::delete("cache_disabled");
        Settings::set("cache_period", 500);

        $_GET["slug"] = $modulePage->slug;
        $_SESSION["language"] = "de";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; "
                . "Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) "
                . "Chrome/63.0.3239.132 Safari/537.36";

        $creator = new PlainTextCreator();

        $output = $creator->render();
        $output = $creator->render();

        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

}
