<?php

use UliCMS\Utils\File;
use UliCMS\Exceptions\DatasetNotFoundException;
use UliCMS\Packages\PackageManager;

class ComparisonsTest extends \PHPUnit\Framework\TestCase {

    protected function setUp(): void {
        $_SERVER = [];
        $_REQUEST = [];
    }

    protected function tearDown(): void {
        Database::query("delete from {prefix}users where username like 'testuser-%'", true);

        $_SERVER = [];
        $_REQUEST = [];

        Settings::set("maintenance_mode", "0");
        chdir(Path::resolve("ULICMS_ROOT"));
        set_format("html");
    }

    // in the test environment this returns always true
    // since the tests are running at the command line
    public function testIsCli() {
        $this->assertTrue(isCLI());
    }

    public function testIsCrawler() {
        $pkg = new PackageManager();

        if (!faster_in_array("CrawlerDetect", $pkg->getInstalledModules())) {
            $this->markTestSkipped("CrawlerDetect is not installed. Skip this test");
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

    public function testIsCrawlerWithoutUseragent() {
        unset($_SERVER["HTTP_USER_AGENT"]);
        $this->assertFalse(
                is_crawler()
        );
    }

    public function testIsCrawlerWithUseragentFromSession() {
        $_SERVER["HTTP_USER_AGENT"] = "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";
        $this->assertTrue(
                is_crawler()
        );
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

        Settings::delete("maintenance_mode");
        $this->assertFalse(isMaintenanceMode());
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

    public function testFasterInArrayReturnsTrue() {
        $array = array("hello", "world", 123);
        $this->assertTrue(faster_in_array("world", $array));
        $this->assertTrue(faster_in_array(123, $array));
    }

    public function testFasterInArrayReturnsFalse() {
        $array = array("hello", "world", 123);
        $this->assertFalse(faster_in_array("germany", $array));
        $this->assertFalse(faster_in_array(789, $array));
        $this->assertFalse(faster_in_array(789, "not-an-array"));
    }

    public function testVarIsType() {
        $this->assertTrue(var_is_type(123, "numeric", true));
        $this->assertTrue(var_is_type(null, "numeric", false));
        $this->assertFalse(var_is_type(null, "numeric", true));
        $this->assertFalse(var_is_type("", "numeric", true));
        $this->assertTrue(var_is_type("", "numeric", false));

        $this->assertFalse(var_is_type("nicht leer", "typ_der_nicht_existiert", true));
    }

    public function testIsNightReturnsTrue() {
        $this->assertTrue(is_night(1570404356));
    }

    public function testIsNightReturnsFalse() {
        $this->assertFalse(is_night(1570389956));
    }

    public function testIsNightWithoutTimeReturnsBool() {
        $this->assertIsBool(is_night());
    }

    public function testIsDebugMode() {
        $this->assertIsBool(is_debug_mode());
    }

    public function testGetByIdThrowsException() {
        $this->expectException(DatasetNotFoundException::class);
        ContentFactory::getByID(PHP_INT_MAX);
    }

    public function testIsVersionNumberReturnsTrue() {
        $this->assertTrue(is_version_number("1.0"));
        $this->assertTrue(is_version_number("123"));
        $this->assertTrue(is_version_number("2.0.3"));
        $this->assertTrue(is_version_number("2.0.3beta"));
    }

    public function testIsVersionNumberReturnsFalse() {
        $this->assertFalse(is_version_number("keine version"));
        $this->assertFalse(is_version_number("null"));
        $this->assertFalse(is_version_number("beta"));
    }

}
