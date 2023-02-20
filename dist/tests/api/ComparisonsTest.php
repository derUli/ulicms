<?php

use App\Utils\File;
use App\Exceptions\DatasetNotFoundException;

class ComparisonsTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $_SERVER = [];
        $_REQUEST = [];
    }

    protected function tearDown(): void
    {
        Database::query("delete from {prefix}users where username like 'testuser-%'", true);

        $_SERVER = [];
        $_REQUEST = [];

        Settings::set('maintenance_mode', "0");
        chdir(Path::resolve("ULICMS_ROOT"));
    }

    // in the test environment this returns always true
    // since the tests are running at the command line
    public function testIsCli()
    {
        $this->assertTrue(is_cli());
    }

    public function testIsCrawler()
    {
        $pkg = new PackageManager();

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

    public function testIsCrawlerWithoutUseragent()
    {
        unset($_SERVER['HTTP_USER_AGENT']);
        $this->assertFalse(
            is_crawler()
        );
    }

    public function testIsCrawlerWithUseragentFromSession()
    {
        $_SERVER['HTTP_USER_AGENT'] = "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";
        $this->assertTrue(
            is_crawler()
        );
    }

    public function testIsAdminDirTrue()
    {
        chdir(Path::resolve("ULICMS_ROOT/admin"));
        $this->assertTrue(is_admin_dir());
    }

    public function testIsAdminDirFalse()
    {
        chdir(Path::resolve("ULICMS_ROOT"));
        $this->assertFalse(is_admin_dir());
    }

    public function testIsMaintenanceModeOn()
    {
        Settings::set('maintenance_mode', "1");
        $this->assertTrue(is_maintenance_mode());
    }

    public function testIsMaintenanceModeOff()
    {
        Settings::set('maintenance_mode', "0");
        $this->assertFalse(is_maintenance_mode());

        Settings::delete('maintenance_mode');
        $this->assertFalse(is_maintenance_mode());
    }

    public function testIsJsonTrue()
    {
        $validJson = file_get_contents(ModuleHelper::buildModuleRessourcePath("core_content", 'metadata.json'));
        $this->assertTrue(is_json($validJson));
    }

    public function testIsJsonFalse()
    {
        $invalidJson = file_get_contents(ModuleHelper::buildModuleRessourcePath("core_content", "lang/de.php"));
        $this->assertFalse(is_json($invalidJson));
    }

    public function testIsDecimalReturnsTrue()
    {
        $this->assertTrue(is_decimal(1.99));
        $this->assertTrue(is_decimal("1.99"));
        $this->assertTrue(is_decimal("0.00"));
        $this->assertTrue(is_decimal("1.00"));
    }

    public function testisDecimalReturnsFalse()
    {
        $this->assertFalse(is_decimal(666));
        $this->assertFalse(is_decimal("666"));
        $this->assertFalse(is_decimal("foobar"));
        $this->assertFalse(is_decimal("0"));
    }

    public function testVarIsType()
    {
        $this->assertTrue(var_is_type(123, "numeric", true));
        $this->assertTrue(var_is_type(null, "numeric", false));
        $this->assertFalse(var_is_type(null, "numeric", true));
        $this->assertFalse(var_is_type('', "numeric", true));
        $this->assertTrue(var_is_type('', "numeric", false));

        $this->assertFalse(var_is_type("nicht leer", "typ_der_nicht_existiert", true));
    }

    public function testGetByIdThrowsException()
    {
        $this->expectException(DatasetNotFoundException::class);
        ContentFactory::getByID(PHP_INT_MAX);
    }

    public function testIsVersionNumberReturnsTrue()
    {
        $this->assertTrue(is_version_number("1.0"));
        $this->assertTrue(is_version_number("123"));
        $this->assertTrue(is_version_number("2.0.3"));
        $this->assertTrue(is_version_number("2.0.3beta"));
    }

    public function testIsVersionNumberReturnsFalse()
    {
        $this->assertFalse(is_version_number("keine version"));
        $this->assertFalse(is_version_number("null"));
        $this->assertFalse(is_version_number("beta"));
    }
}
