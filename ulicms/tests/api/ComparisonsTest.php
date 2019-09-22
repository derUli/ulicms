<?php

use UliCMS\Utils\File;

class ComparisonsTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $this->cleanUp();
        @session_start();
    }

    public function tearDown() {
        $this->cleanUp();
        Database::query("delete from {prefix}users where username like 'testuser-%'", true);
        unset($_SESSION["login_id"]);
        @session_destroy();
    }

    public function cleanUp() {
        unset($_REQUEST["action"]);
        Settings::set("maintenance_mode", "0");
        chdir(Path::resolve("ULICMS_ROOT"));
        set_format("html");
        unset($_SESSION["csrf_token"]);
        unset($_REQUEST["csrf_token"]);
    }

    // in the test environment this returns always true
    // since the tests are running at the command line
    public function testIsCli() {
        $this->assertTrue(isCLI());
    }

    public function testIsCrawler() {
        $pkg = new PackageManager();
        if (!faster_in_array("CrawlerDetect", $pkg->getInstalledModules())) {
            $this->assertNotNull("CrawlerDetect is not installed. Skip this test");
            return;
        }
        $useragents = array(
            "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" => true,
            "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)" => true,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36" => false,
            "Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)" => true,
            "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; NP08; .NET4.0C; .NET4.0E; NP08; MAAU; rv:11.0) like Gecko" => false,
            "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0" => false
        );
        foreach ($useragents as $key => $value) {
            $this->assertEquals($value, is_crawler($key));
        }
    }

    public function testIsTrue() {
        $this->assertTrue(is_true(true));
        $this->assertTrue(is_true(1));
        $this->assertFalse(is_true($nothing));
        $this->assertFalse(is_true(false));
        $this->assertFalse(is_true(0));
    }

    public function testIsAdminDirTrue() {
        chdir(Path::resolve("ULICMS_ROOT/admin"));
        $this->assertTrue(is_admin_dir());
    }

    public function testIsAdminDirFalse() {
        chdir(Path::resolve("ULICMS_ROOT"));
        $this->assertFalse(is_admin_dir());
    }

    public function testIsMaintenanceModeOn() {
        Settings::set("maintenance_mode", "1");
        $this->assertTrue(isMaintenanceMode());
    }

    public function testIsMaintenanceModeOff() {
        Settings::set("maintenance_mode", "0");
        $this->assertFalse(isMaintenanceMode());
    }

    public function testIsFalse() {
        $this->assertFalse(is_false(true));
        $this->assertFalse(is_false(1));
        $this->assertTrue(is_false($nothing));
        $this->assertTrue(is_false(false));
        $this->assertTrue(is_false(0));
    }

    public function testIsJsonTrue() {
        $validJson = File::read(ModuleHelper::buildModuleRessourcePath("core_content", "metadata.json"));
        $this->assertTrue(is_json($validJson));
    }

    public function testIsJsonFalse() {
        $invalidJson = File::read(ModuleHelper::buildModuleRessourcePath("core_content", "lang/de.php"));
        $this->assertFalse(is_json($invalidJson));
    }

    public function testIsNumericArray() {
        $this->assertTrue(is_numeric_array(array(
            "42",
            1337,
            0x539,
            02471,
            0b10100111001,
            1337e0,
            9.1
        )));
        $this->assertFalse(is_numeric_array(array(
            "42",
            "foo",
            "not numeric",
            1337
        )));
        $this->assertFalse(is_numeric_array("Not an array"));
        $this->assertFalse(is_numeric_array(42));
        $this->assertFalse(is_numeric_array(9.1));
    }

    public function testIsZeroReturnsTrue() {
        $this->assertTrue(is_zero(0.00));
        $this->assertTrue(is_zero(0));
        $this->assertTrue(is_zero("0.00"));
        $this->assertTrue(is_zero("0"));
    }

    public function testIsZeroReturnsFalse() {
        $this->assertFalse(is_zero(2.99));
        $this->assertFalse(is_zero(4));
        $this->assertFalse(is_zero("13.37"));
        $this->assertFalse(is_zero("666"));
        $this->assertFalse(is_zero("foobar"));
        $this->assertFalse(is_zero(null));
    }

    public function testIsDecimalReturnsTrue() {
        $this->assertTrue(is_decimal(1.99));
        $this->assertTrue(is_decimal("1.99"));
        $this->assertTrue(is_decimal("0.00"));
        $this->assertTrue(is_decimal("1.00"));
    }

    public function testisDecimalReturnsFalse() {
        $this->assertFalse(is_decimal(666));
        $this->assertFalse(is_decimal("666"));
        $this->assertFalse(is_decimal("foobar"));
        $this->assertFalse(is_decimal("0"));
    }
    public function testIsBlankReturnsTrue() {
        $this->assertTrue(is_blank(""));
        $this->assertTrue(is_blank(" "));
        $this->assertTrue(is_blank(false));
        $this->assertTrue(is_blank(null));
        $this->assertTrue(is_blank(0));
        $this->assertTrue(is_blank([]));
        $this->assertTrue(is_blank("0"));
        $this->assertTrue(is_blank($notDefined));
    }

    public function testIsBlankReturnsFalse() {
        $this->assertFalse(is_blank(" hallo welt "));
        $this->assertFalse(is_blank(13));
        $this->assertFalse(is_blank(true));
        $this->assertFalse(is_blank(array("foo", "bar")));
        $this->assertFalse(is_blank("13"));
    }

    public function testIsPresentReturnsTrue() {
        $this->assertTrue(is_present(" hallo welt "));
        $this->assertTrue(is_present(13));
        $this->assertTrue(is_present(true));
        $this->assertTrue(is_present(array("foo", "bar")));
        $this->assertTrue(is_present("13"));
    }

    public function testIsPresentReturnsFalse() {
        $this->assertFalse(is_present(""));
        $this->assertFalse(is_present(" "));
        $this->assertFalse(is_present(false));
        $this->assertFalse(is_present(null));
        $this->assertFalse(is_present(0));
        $this->assertFalse(is_present([]));
        $this->assertFalse(is_present("0"));
        $this->assertFalse(is_present($undefinedVar));
    }

    public function testStartsWithReturnsTrue() {
        $this->assertTrue(startsWith("hello world", "hello"));
        $this->assertTrue(startsWith("hello world", "Hello", false));
    }

    public function testStartsWithReturnsFalse() {
        $this->assertFalse(startsWith("hello world", "bye"));
        $this->assertFalse(startsWith("hello world", "Hello"));
    }

    public function testEndsWithReturnsTrue() {
        $this->assertTrue(endsWith("hello world", "world"));
        $this->assertTrue(endsWith("hello world", "World", false));
    }

    public function testEndsWithReturnsFalse() {
        $this->assertFalse(endsWith("hello world", "you"));
        $this->assertFalse(endsWith("hello world", "World"));
    }
}
