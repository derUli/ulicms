<?php

use App\Registries\HelperRegistry;

class AntispamHelperTest extends \PHPUnit\Framework\TestCase
{
    private $initialCountryBlacklist;

    protected function setUp(): void
    {
        HelperRegistry::loadModuleHelpers();
        $this->initialCountryBlacklist = Settings::get("country_blacklist");
        Settings::set("spamfilter_enabled", "yes");
    }

    protected function tearDown(): void
    {
        Settings::set("country_blacklist", $this->initialCountryBlacklist);
        $_SERVER = [];
        Settings::set("spamfilter_enabled", "yes");
    }

    public function testIsChineseReturnsTrue()
    {
        // Only chinese
        $this->assertTrue(AntiSpamHelper::isChinese("这只是一个简单的文字"));
        // Mixed Latin and Chinese
        $this->assertTrue(AntiSpamHelper::isChinese("Deutsche 这只是一个简单的文字
 Büchstäbem"));
        // korean
    }

    public function testIsChineseReturnsFalse()
    {
        // Only Latin
        $this->assertFalse(AntiSpamHelper::isChinese("Deutsche Büchstäben"));
        // korean
        $this->assertFalse(AntiSpamHelper::isChinese("이것은 단순한 텍스트입니다."));
        // Russian
        $this->assertFalse(AntiSpamHelper::isChinese("Это просто простой текст"));
        // with null
        $this->assertFalse(AntiSpamHelper::isChinese(null));
    }

    public function testIsCyrillicReturnsTrue()
    {
        // Only cyrillic
        $this->assertTrue(AntiSpamHelper::isCyrillic("Это просто простой текст"));
        // Mixed Latin and Cyrillic
        $this->assertTrue(AntiSpamHelper::isCyrillic("Deutsche Это просто простой текст
 Büchstäbem"));
        // Ukrainian
        $this->assertTrue(AntiSpamHelper::isCyrillic("Це просто текст"));
    }

    public function testIsCyrillicReturnsFalse()
    {
        // Only Latin
        $this->assertFalse(AntiSpamHelper::isCyrillic("Deutsche Büchstäben"));
        // korean
        $this->assertFalse(AntiSpamHelper::isCyrillic("이것은 단순한 텍스트입니다."));
        // with null
        $this->assertFalse(AntiSpamHelper::isCyrillic(null));
    }

    public function testIsRtlReturnsTrue()
    {
        $this->assertTrue(AntiSpamHelper::isRtl("ایران یک دولت نیست"));
        $this->assertTrue(AntiSpamHelper::isRtl("אין אלוהים."));
        $this->assertTrue(AntiSpamHelper::isRtl("لا يوجد إله."));
        $this->assertTrue(AntiSpamHelper::isRtl("לופטים"));
    }

    public function testIsRtlReturnsFalse()
    {
        $this->assertFalse(AntiSpamHelper::isRtl("There is no god."));
        $this->assertFalse(AntiSpamHelper::isRtl("Es gibt keinen Gott."));
        $this->assertFalse(AntiSpamHelper::isRtl("Немає бога."));
        $this->assertFalse(AntiSpamHelper::isRtl("没有上帝。"));

        // with null
        $this->assertFalse(AntiSpamHelper::isRtl(null));
    }

    public function testIsCountryBlockedReturnsTrue()
    {
        Settings::set("country_blacklist", "vn,jp,at,tr");

        // Vietnam
        $_SERVER["REMOTE_ADDR"] = "123.30.54.106";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());

        // Japan
        $_SERVER["REMOTE_ADDR"] = "202.172.26.11";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());

        // Austria
        $_SERVER["REMOTE_ADDR"] = "194.116.243.20";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());

        // Turkey
        $_SERVER["REMOTE_ADDR"] = "88.255.55.110";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());
    }

    public function testIsCountryBlockedReturnsFalse()
    {
        Settings::set("country_blacklist", "vn,jp,at,tr");

        // Germany
        $_SERVER["REMOTE_ADDR"] = "178.254.29.67";
        $this->assertFalse(AntiSpamHelper::isCountryBlocked());

        // Italy
        $_SERVER["REMOTE_ADDR"] = "40.84.199.233";
        $this->assertFalse(AntiSpamHelper::isCountryBlocked());

        // United Kingdom
        $_SERVER["REMOTE_ADDR"] = "52.222.250.185";
        $this->assertFalse(AntiSpamHelper::isCountryBlocked());

        $_SERVER["REMOTE_ADDR"] = "not an ip address";
        $this->assertFalse(AntiSpamHelper::isCountryBlocked());

        $this->assertFalse(
            AntiSpamHelper::isCountryBlocked(
                "175.45.176.0",
                ["kp"]
            )
        );
    }

    public function testContainsBadWordsReturnsWord()
    {
        $this->assertEquals("viagra", AntiSpamHelper::containsBadwords("Buy cheap Viagra pills."));
    }

    public function testContainsBadWordsReturnsNull()
    {
        $this->assertNull(AntiSpamHelper::containsBadwords("This is a clean text without spammy words"));
    }

    public function testContainsBadWordsWithoutInputStringReturnsNull()
    {
        $this->assertNull(AntiSpamHelper::containsBadwords(null));
    }

    public function testIsSpamFilterEnabledReturnsTrue()
    {
        Settings::set("spamfilter_enabled", "yes");
        $this->assertTrue(AntiSpamHelper::isSpamFilterEnabled());
    }

    public function testIsSpamFilterEnabledReturnsFalse()
    {
        Settings::delete("spamfilter_enabled", "yes");
        $this->assertFalse(AntiSpamHelper::isSpamFilterEnabled());
    }

    public function testCheckForBotReturnsTrue()
    {
        $this->assertTrue(AntiSpamHelper::checkForBot("libwww-perl/5.65"));
        $this->assertTrue(AntiSpamHelper::checkForBot("Mozilla/4.0 (compatible; Win32; WinHttp.WinHttpRequest.5"));
    }

    public function testCheckForBotWithoutArgumentReturnsTrue()
    {
        $_SERVER['HTTP_USER_AGENT'] = "libwww-perl/5.65";
        $this->assertTrue(AntiSpamHelper::checkForBot());
    }

    public function testCheckForBotWithoutUseragentReturnsTrue()
    {
        $this->assertFalse(AntiSpamHelper::checkForBot());
    }

    public function testCheckForBotReturnsFalse()
    {
        $this->assertFalse(AntiSpamHelper::checkForBot("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36"));
        $this->assertFalse(AntiSpamHelper::checkForBot("Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0"));
    }

    public function testCheckMailDomainMxReturnsTrue()
    {
        // some mail addresses by common freemail providers
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@web.de"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@gmx.net"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@1und1.de"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@yahoo.com"));

        // some valid mail addresses
        $this->assertTrue(AntiSpamHelper::checkMailDomain("support@ulicms.de"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("spiegel_online@spiegel.de"));
    }

    public function testCheckMailDomainMxReturnsFalse()
    {
        // non existing domain
        $this->assertFalse(AntiSpamHelper::checkMailDomain("john.doe@thisisnotadomain.de"));

        // Valid domain without an MX entry
        $this->assertFalse(AntispamHelper::checkMailDomain("shittyspammer@ftp.gnu.org"));
    }
}
