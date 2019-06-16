<?php

use Phpfastcache\Helper\Psr16Adapter;

class CacheUtilTest extends \PHPUnit\Framework\TestCase {

    private $cacheDisabledOriginal;
    private $cachePeriodOriginal;

    public function setUp() {
        $this->cacheDisabledOriginal = Settings::get("cache_disabled");
        $this->cachePeriodOriginal = Settings::get("cache_period");
        Settings::delete("cache_disabled");
    }

    public function tearDown() {
        if ($this->cacheDisabledOriginal) {
            Settings::set("cache_disabled", "yes");
        } else {
            Settings::delete("cache_disabled");
        }
        unset($_SESSION["logged_in"]);
        Settings::set("cache_period", $this->cachePeriodOriginal);
    }

    public function testIsCacheEnabled() {
        Settings::delete("cache_disabled");
        $this->assertTrue(CacheUtil::isCacheEnabled());

        Settings::set("cache_disabled", "yes");
        $this->assertFalse(CacheUtil::isCacheEnabled());

        Settings::delete("cache_disabled");
    }

    public function testIsCacheEnabledLoggedIn() {
        $_SESSION["logged_in"] = true;
        Settings::delete("cache_disabled");
        $this->assertFalse(CacheUtil::isCacheEnabled());

        Settings::set("cache_disabled", "yes");
        $this->assertFalse(CacheUtil::isCacheEnabled());
        unset($_SESSION["logged_in"]);
        Settings::delete("cache_disabled");
    }

    public function testGetAdapter() {
        $this->assertInstanceOf(Psr16Adapter::class, CacheUtil::getAdapter());
    }

    public function testGetCachePeriod() {
        Settings::set("cache_period", 123);
        $this->assertEquals(123, CacheUtil::getCachePeriod());
        Settings::set("cache_period", 456);
        $this->assertEquals(456, CacheUtil::getCachePeriod());
        Settings::set("cache_period", 0);
        $this->assertEquals(0, CacheUtil::getCachePeriod());
    }

    public function testGetCurrentUid() {
        $_SERVER["REQUEST_URI"] = "/my-url.html";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1";
        $_SESSION["language"] = "de";
        $this->assertEquals("03f3212f898cd71615a9dc03bdb0c2f1", CacheUtil::getCurrentUid());

        $_SESSION["language"] = "en";
        $this->assertEquals("dae3163884e1d91690d9f525dd559820", CacheUtil::getCurrentUid());

        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5";
        $this->assertEquals("ea537739024d4680137192a32f8e4d66", CacheUtil::getCurrentUid());

        $_SERVER["REQUEST_URI"] = "/other-url.html?param=value";
        $this->assertEquals("f613a13d544c757b5b015de5dabd561b", CacheUtil::getCurrentUid());
    }

    // Tests for a bug where two different requests are producing the same hash
    public function testGetCurrentUidCollisionBug() {
        $_SERVER["REQUEST_URI"] = "/my-url.html";
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3";
        $_SESSION["language"] = "de";

        $uid1 = CacheUtil::getCurrentUid();

        $_SERVER["REQUEST_URI"] = "/my-url.html";
        $_SERVER["HTTP_USER_AGENT"] = "Googlebot/2.1 (+http://www.google.com/bot.html)";
        $_SESSION["language"] = "de";

        $uid2 = CacheUtil::getCurrentUid();

        $this->assertNotEquals($uid2, $uid1);
    }

}
