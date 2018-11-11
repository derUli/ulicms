<?php

class AntispamHelperTest extends \PHPUnit\Framework\TestCase
{

    private $initialCountryBlacklist;

    public function setUp()
    {
        $this->initialCountryBlacklist = Settings::get("country_blacklist");
        Settings::set("spamfilter_enabled", "yes");
    }

    public function tearDown()
    {
        Settings::set("country_blacklist", $this->initialCountryBlacklist);
        unset($_SERVER["REMOTE_ADDR"]);
        Settings::set("spamfilter_enabled", "yes");
    }

    public function testIsChinese()
    {
        // Only Latin
        $this->assertFalse(AntiSpamHelper::isChinese("Deutsche Büchstäben"));
        // Only chinese
        $this->assertTrue(AntiSpamHelper::isChinese("这只是一个简单的文字"));
        // Mixed Latin and Chinese
        $this->assertTrue(AntiSpamHelper::isChinese("Deutsche 这只是一个简单的文字
 Büchstäbem"));
        // korean
        $this->assertFalse(AntiSpamHelper::isChinese("이것은 단순한 텍스트입니다."));
        // Russian
        $this->assertFalse(AntiSpamHelper::isChinese("Это просто простой текст"));
    }

    public function testIsCyrillic()
    {
        // Only Latin
        $this->assertFalse(AntiSpamHelper::isCyrillic("Deutsche Büchstäben"));
        // Only cyrillic
        $this->assertTrue(AntiSpamHelper::isCyrillic("Это просто простой текст"));
        // Mixed Latin and Cyrillic
        $this->assertTrue(AntiSpamHelper::isCyrillic("Deutsche Это просто простой текст
 Büchstäbem"));
        // korean
        $this->assertFalse(AntiSpamHelper::isCyrillic("이것은 단순한 텍스트입니다."));
        // Ukrainian
        $this->assertTrue(AntiSpamHelper::isCyrillic("Це просто текст"));
    }

    // TODO: Implement test for isCountryBlocked
    public function testIsCountryBlocked()
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
        
        // Vietnam
        $_SERVER["REMOTE_ADDR"] = "123.30.54.106";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());
        
        // Japan
        $_SERVER["REMOTE_ADDR"] = "183.79.23.196";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());
        
        // Austria
        $_SERVER["REMOTE_ADDR"] = "194.116.243.20";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());
        
        // Turkey
        $_SERVER["REMOTE_ADDR"] = "88.255.55.110";
        $this->assertTrue(AntiSpamHelper::isCountryBlocked());
    }

    public function testContainsBadWords()
    {
        $this->assertNull(AntiSpamHelper::containsBadwords("This is a clean text without spammy words"));
        $this->assertEquals("viagra", AntiSpamHelper::containsBadwords("Buy cheap Viagra pills."));
    }

    public function testIsSpamFilterEnabled()
    {
        Settings::delete("spamfilter_enabled", "yes");
        $this->assertFalse(AntiSpamHelper::isSpamFilterEnabled());
        Settings::set("spamfilter_enabled", "yes");
        $this->assertTrue(AntiSpamHelper::isSpamFilterEnabled());
    }

    public function testCheckForBot()
    {
        $this->assertTrue(AntiSpamHelper::checkForBot("libwww-perl/5.65"));
        $this->assertTrue(AntiSpamHelper::checkForBot("Mozilla/4.0 (compatible; Win32; WinHttp.WinHttpRequest.5"));
        
        $this->assertFalse(AntiSpamHelper::checkForBot("Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36"));
        $this->assertFalse(AntiSpamHelper::checkForBot("Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0"));
    }

    public function testCheckMailDomainMx()
    {
        // some mail addresses by common freemail providers
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@web.de"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@gmx.net"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@1und1.de"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("mymail@yahoo.com"));
        
        // some valid mail addresses
        $this->assertTrue(AntiSpamHelper::checkMailDomain("support@ulicms.de"));
        $this->assertTrue(AntiSpamHelper::checkMailDomain("spiegel_online@spiegel.de"));
        
        // non existing domain
        $this->assertFalse(AntiSpamHelper::checkMailDomain("john.doe@thisisnotadomain.de"));
        
        // Valid domain without an MX entry
        $this->assertFalse(AntispamHelper::checkMailDomain("shittyspammer@example.org"));
    }
}
	