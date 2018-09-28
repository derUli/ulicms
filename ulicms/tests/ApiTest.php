<?php
include_once Path::Resolve("ULICMS_ROOT/templating.php");

class ApiTest extends \PHPUnit\Framework\TestCase
{

    public function testRemovePrefix()
    {
        $this->assertEquals("my_bar", remove_prefix("foo_my_bar", "foo_"));
        $this->assertEquals("my_foo_bar", remove_prefix("foo_my_foo_bar", "foo_"));
    }

    public function testIsCrawler()
    {
        $pkg = new PackageManager();
        if (! faster_in_array("CrawlerDetect", $pkg->getInstalledModules())) {
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

    public function testGetAllUsedLanguages()
    {
        $languages = getAllUsedLanguages();
        $this->assertGreaterThanOrEqual(2, count($languages));
        $this->assertTrue(in_array("de", $languages));
        $this->assertTrue(in_array("en", $languages));
    }

    public function testAddTranslation()
    {
        $key1 = uniqid();
        $key2 = "TRANSLATION_" . uniqid();
        $value1 = uniqid();
        $value2 = uniqid();
        $this->assertEquals($key1, get_translation($key1));
        add_translation($key1, $value1);
        $this->assertEquals($value1, get_translation($key1));
        add_translation($key1, $value2);
        $this->assertEquals($value1, get_translation($key1));
        add_translation($key2, $value2);
        $this->assertEquals($value2, constant(strtoupper($key2)));
    }

    public function testGetModuleMeta()
    {
        $this->assertEquals("core", getModuleMeta("core_home", "source"));
        $meta = getModuleMeta("core_home");
        $this->assertEquals("models/HomeViewModel.php", $meta["objects"]["HomeViewModel"]);
        $this->assertFalse($meta["embed"]);
        $this->assertNull(getModuleMeta("not_a_module"));
        $this->assertNull(getModuleMeta("not_a_module", "version"));
        $this->assertNull(getModuleMeta("core_home", "not_here"));
    }

    public function testBool2YesNo()
    {
        $this->assertEquals(get_translation("yes"), bool2YesNo(1));
        $this->assertEquals(get_translation("no"), bool2YesNo(0));
        $this->assertEquals(get_translation("yes"), bool2YesNo(true));
        $this->assertEquals(get_translation("no"), bool2YesNo(false));
        
        $this->assertEquals("cool", bool2YesNo(1, "cool", "doof"));
        $this->assertEquals("doof", bool2YesNo(0, "cool", "doof"));
        $this->assertEquals("cool", bool2YesNo(true, "cool", "doof"));
        $this->assertEquals("doof", bool2YesNo(false, "cool", "doof"));
    }

    public function testGetMime()
    {
        $this->assertEquals("text/plain", get_mime(Path::resolve("ULICMS_ROOT/.htaccess")));
        $this->assertEquals("image/gif", get_mime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.gif")));
        $this->assertEquals("image/png", get_mime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.png")));
    }

    public function testIsTrue()
    {
        $this->assertTrue(is_true(true));
        $this->assertTrue(is_true(1));
        $this->assertFalse(is_true($nothing));
        $this->assertFalse(is_true(false));
        $this->assertFalse(is_true(0));
    }

    public function testIsFalse()
    {
        $this->assertFalse(is_false(true));
        $this->assertFalse(is_false(1));
        $this->assertTrue(is_false($nothing));
        $this->assertTrue(is_false(false));
        $this->assertTrue(is_false(0));
    }

    public function testIsJson()
    {
        $validJson = File::read(ModuleHelper::buildModuleRessourcePath("core_content", "metadata.json"));
        $invalidJson = File::read(ModuleHelper::buildModuleRessourcePath("core_content", "lang/de.php"));
        
        $this->assertTrue(is_json($validJson));
        $this->assertFalse(is_json($invalidJson));
    }

    public function testIsNumericArray()
    {
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

    public function testVarIsType()
    {
        $this->assertTrue(var_is_type(123, "numeric", true));
        $this->assertTrue(var_is_type(null, "numeric", false));
        $this->assertFalse(var_is_type(null, "numeric", true));
        $this->assertFalse(var_is_type("", "numeric", true));
        $this->assertTrue(var_is_type("", "numeric", false));
    }
}
