<?php

class ApiTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        $this->cleanUp();
        @session_start();
    }

    public function tearDown() {
        $this->cleanUp();
        Database::query("delete from {prefix}users where username like 'testuser-%", true);
        @session_destroy();
    }

    public function cleanUp() {
        unset($_REQUEST["action"]);
        Settings::set("maintenance_mode", "0");
        chdir(Path::resolve("ULICMS_ROOT"));
        set_format("html");
        unseT($_SESSION["csrf_token"]);
        unset($_REQUEST["csrf_token"]);
    }

    public function testRemovePrefix() {
        $this->assertEquals("my_bar", remove_prefix("foo_my_bar", "foo_"));
        $this->assertEquals("my_foo_bar", remove_prefix("foo_my_foo_bar", "foo_"));
    }

    public function testRemoveSuffix() {
        $this->assertEquals("Hello", remove_suffix("Hello World!", " World!"));
        $this->assertEquals("Foo", remove_suffix("FooBar", "Bar"));
        $this->assertEquals("file", remove_suffix("file.txt", ".txt"));
        $this->assertEquals("FooBar", remove_suffix("FooBar", "Foo"));
        $this->assertEquals("", remove_suffix("Foo", "Foo"));
        $this->assertEquals("Foo", remove_suffix("Foo", "Hello"));
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

    public function testGetAllUsedLanguages() {
        $languages = getAllUsedLanguages();
        $this->assertGreaterThanOrEqual(2, count($languages));
        $this->assertTrue(in_array("de", $languages));
        $this->assertTrue(in_array("en", $languages));
    }

    public function testAddTranslation() {
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

    public function testGetModuleMeta() {
        $this->assertEquals("core", getModuleMeta("core_home", "source"));
        $meta = getModuleMeta("core_home");
        $this->assertEquals("models/HomeViewModel.php", $meta["objects"]["HomeViewModel"]);
        $this->assertFalse($meta["embed"]);
        $this->assertNull(getModuleMeta("not_a_module"));
        $this->assertNull(getModuleMeta("not_a_module", "version"));
        $this->assertNull(getModuleMeta("core_home", "not_here"));
    }

    public function testBool2YesNo() {
        $this->assertEquals(get_translation("yes"), bool2YesNo(1));
        $this->assertEquals(get_translation("no"), bool2YesNo(0));
        $this->assertEquals(get_translation("yes"), bool2YesNo(true));
        $this->assertEquals(get_translation("no"), bool2YesNo(false));

        $this->assertEquals("cool", bool2YesNo(1, "cool", "doof"));
        $this->assertEquals("doof", bool2YesNo(0, "cool", "doof"));
        $this->assertEquals("cool", bool2YesNo(true, "cool", "doof"));
        $this->assertEquals("doof", bool2YesNo(false, "cool", "doof"));
    }

    public function testGetMime() {
        $this->assertEquals("text/plain", get_mime(Path::resolve("ULICMS_ROOT/.htaccess")));
        $this->assertEquals("image/gif", get_mime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.gif")));
        $this->assertEquals("image/png", get_mime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.png")));
    }

    public function testIsTrue() {
        $this->assertTrue(is_true(true));
        $this->assertTrue(is_true(1));
        $this->assertFalse(is_true($nothing));
        $this->assertFalse(is_true(false));
        $this->assertFalse(is_true(0));
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

    public function testVarIsType() {
        $this->assertTrue(var_is_type(123, "numeric", true));
        $this->assertTrue(var_is_type(null, "numeric", false));
        $this->assertFalse(var_is_type(null, "numeric", true));
        $this->assertFalse(var_is_type("", "numeric", true));
        $this->assertTrue(var_is_type("", "numeric", false));
    }

    public function testStrContainsTrue() {
        $this->assertTrue(str_contains("Ananas", "Ich esse gerne Ananas."));
    }

    public function testStrContainsFalse() {
        $this->assertFalse(str_contains("Tomaten", "Ich esse gerne Ananas."));
    }

    public function testGetActionIsSet() {
        $_REQUEST["action"] = "pages";
        $this->assertEquals("pages", get_action());
        unset($_REQUEST["action"]);
    }

    public function testGetActionIsNotSet() {
        $this->assertEquals("home", get_action());
    }

    public function testIsMaintenanceModeOn() {
        Settings::set("maintenance_mode", "1");
        $this->assertTrue(isMaintenanceMode());
    }

    public function testIsMaintenanceModeOff() {
        Settings::set("maintenance_mode", "0");
        $this->assertFalse(isMaintenanceMode());
    }

    public function testGetStringLengthInBytes() {
        $this->assertEquals(39, getStringLengthInBytes("Das ist die Lösung für die Änderung."));
    }

    public function testIsAdminDirTrue() {
        chdir(Path::resolve("ULICMS_ROOT/admin"));
        $this->assertTrue(is_admin_dir());
    }

    public function testIsAdminDirFalse() {
        chdir(Path::resolve("ULICMS_ROOT"));
        $this->assertFalse(is_admin_dir());
    }

    public function testSetFormat() {
        set_format("pdf");
        $this->assertEquals("pdf", $_GET["format"]);

        set_format("txt");
        $this->assertEquals("txt", $_GET["format"]);
    }

    public function testGetJqueryUrl() {
        $this->assertEquals("node_modules/jquery/dist/jquery.min.js", get_jquery_url());
    }

    public function testCheckCsrfTokenNoToken() {
        unset($_SESSION["csrf_token"]);
        $this->assertFalse(check_csrf_token());
    }

    public function testCheckCsrfTokenValid() {
        $token = get_csrf_token();
        $this->assertNotNull($token);

        $_REQUEST["csrf_token"] = $token;

        $this->assertTrue(check_csrf_token());

        unset($_SESSION["csrf_token"]);
        unset($_REQUEST["csrf_token"]);
    }

    public function testCheckCsrfTokenInvalid() {
        $token = get_csrf_token();
        $_REQUEST["csrf_token"] = "thisisnotthetoken";
        $this->assertFalse(check_csrf_token());
        unset($_SESSION["csrf_token"]);
    }

    public function testPreparePlainTextforHTMLOutput() {
        $input = "This is\na\n<Textfile>.";
        $expected = "This is<br />\na<br />\n&lt;Textfile&gt;.";
        $this->assertEquals($expected, preparePlainTextforHTMLOutput($input));
    }

    // in the test environment this returns always true
    // since the tests are running at the command line
    public function testIsCli() {
        $this->assertTrue(isCLI());
    }

    public function testRandStr() {
        $password1 = rand_string(15);
        $password2 = rand_string(15);
        $password3 = rand_string(12);
        $this->assertEquals(15, strlen($password1));
        $this->assertEquals(15, strlen($password2));
        $this->assertEquals(12, strlen($password3));
        $this->assertNotEquals($password2, $password1);
    }

    public function testSplitAndTrim() {
        $input = "Max; Muster; max@muster.de ; Musterstadt";
        $result = splitAndTrim($input);
        $this->assertEquals("Max", $result[0]);
        $this->assertEquals("Muster", $result[1]);
        $this->assertEquals("max@muster.de", $result[2]);
        $this->assertEquals("Musterstadt", $result[3]);
    }

    public function testGetThemesList() {
        $themes = getThemesList();
        $this->assertContains("impro17", $themes);
    }

    public function testGetPageSystemnameByID() {
        $this->assertEquals($first->systemname, getPageSystemnameByID($first->id));
        $this->assertNull(getPageSystemnameByID(PHP_INT_MAX));
    }

    public function testGetPageIDBySystemname() {
        $allPages = ContentFactory::getAll();
        $first = $allPages[0];
        $this->assertEquals($first->id, getPageIDBySystemname($first->systemname));
        $this->assertNull(getPageIDBySystemname("ich-existiere-wirklich-nicht"));
    }

    public function testGetPageTitleByID() {
        $allPages = ContentFactory::getAll();
        $first = $allPages[0];
        $this->assertEquals($first->title, getPageTitleByID($first->id));
        $this->assertEquals("[" . get_translation("none") . "]", getPageTitleByID(PHP_INT_MAX));
    }

    public function testGetPreferredLanguage() {
        $acceptLanguageHeader1 = "Accept-Language: da, en-gb;q=0.8, en;q=0.7, de;q=0.5";
        $this->assertEquals("en", get_prefered_language(array("de", "en"), $acceptLanguageHeader1));

        $acceptLanguageHeader2 = "Accept-Language: da, en-gb;q=0.8, en;q=0.7, de;q=0.9";
        $this->assertEquals("de", get_prefered_language(array("de", "en"), $acceptLanguageHeader2));
    }

    public function testGetHtmlEditorReturnsNull() {
        if (session_id()) {
            @session_destroy();
        }
        $this->assertNull(get_html_editor());
    }

    public function testGetHtmlEditorReturnsCKEditor() {

        $user = new User();
        $user->setUsername("testuser-1");
        $user->setPassword(rand_string(23));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
        $user->setHTMLEditor("ckeditor");
        $user->save();

        @session_start();
        register_session(getUserByName(("testuser-1")), false);
        $this->assertEquals("ckeditor", get_html_editor());
        @session_destroy();
    }

    public function testGetHtmlEditorReturnsCodeMirror() {
        $user = new User();
        $user->setUsername("testuser-2");
        $user->setPassword(rand_string(666));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
        $user->setHTMLEditor("codemirror");
        $user->save();

        @session_start();
        register_session(getUserByName(("testuser-2")), false);
        $this->assertEquals("codemirror", get_html_editor());
        @session_destroy();
    }

}
