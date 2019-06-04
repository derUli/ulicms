<?php

use UliCMS\Utils\File;

class ComparisonsTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $this->cleanUp();
        @session_start();
    }

    public function tearDown() {
        $this->cleanUp();
        Database::query("delete from {prefix}users where username like 'testuser-%", true);
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

}
