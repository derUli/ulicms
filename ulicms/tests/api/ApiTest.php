<?php

class ApiTest extends \PHPUnit\Framework\TestCase {

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

    public function testGetStringLengthInBytes() {
        $this->assertEquals(39, getStringLengthInBytes("Das ist die Lösung für die Änderung."));
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

    public function testGetPageSlugByID() {
        $this->assertEquals($first->slug, getPageSlugByID($first->id));
        $this->assertNull(getPageSlugByID(PHP_INT_MAX));
    }

    public function testGetPageIDBySlug() {
        $allPages = ContentFactory::getAll();
        $first = $allPages[0];
        $this->assertEquals($first->id, getPageIDBySlug($first->slug));
        $this->assertNull(getPageIDBySlug("ich-existiere-wirklich-nicht"));
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

    public function testIsBlankReturnsTrue() {
        $this->assertTrue(is_blank(""));
        $this->assertTrue(is_blank("  "));
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
        $this->assertFalse(is_present("  "));
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

    public function testIdefine() {
        $this->assertFalse(defined("test_hello"));

        $this->assertTrue(idefine("TEST_HELLO", "World"));
        $this->assertEquals("World", TEST_HELLO);

        $this->assertFalse(idefine("TEST_HELLO", "Uli"));
        $this->assertEquals("World", TEST_HELLO);
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
    }

    public function testGetAllSlugs() {
        $slugs = getAllSlugs();
        $this->assertTrue(in_array("willkommen", $slugs));
        $this->assertTrue(in_array("welcome", $slugs));
        $this->assertTrue(in_array("lorem_ipsum", $slugs));
    }

    public function testGetAllSlugsByLanguage() {
        $germanSlugs = getAllSlugs("de");
        $this->assertTrue(in_array("willkommen", $germanSlugs));
        $this->assertFalse(in_array("welcome", $germanSlugs));
        $this->assertTrue(in_array("glueckskeks", $germanSlugs));
        $this->assertFalse(in_array("fortune", $germanSlugs));

        $englishSlugs = getAllSlugs("en");
        $this->assertTrue(in_array("welcome", $englishSlugs));
        $this->assertFalse(in_array("willkommen", $englishSlugs));
        $this->assertTrue(in_array("fortune", $englishSlugs));
        $this->assertFalse(in_array("glueckskeks", $englishSlugs));
    }

    public function testIsAdminReturnsFalse() {
        $user = new User();
        $user->setUsername("testuser-nicht-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Nicht");
        $user->setPassword(uniqid());
        $user->setAdmin(false);
        $user->save();

        $_SESSION["login_id"] = $user->getId();

        $this->assertFalse(is_admin());
    }

    public function testIsAdminReturnsTrue() {
        $user = new User();
        $user->setUsername("testuser-ist-admin");
        $user->setLastname("Admin");
        $user->setFirstname("Ist");
        $user->setPassword(uniqid());
        $user->setAdmin(true);
        $user->save();

        $_SESSION["login_id"] = $user->getId();

        $this->assertTrue(is_admin());
    }

    public function testGetAllUsedMenus() {
        $menus = get_all_used_menus();
        $this->assertCount(1, $menus);
        $this->isTrue(in_array("top", $menus));
        $this->isFalse(in_array("left", $menus));
    }

    public function testCmsVersion() {
        $this->assertTrue(version_compare(cms_version(), "2019.2",
                        ">"));
    }

    public function testGetEnvironment() {
        $this->assertEquals("test", get_environment());
    }

    public function testIsModuleInstalledReturnsTrue() {
        $this->assertTrue(isModuleInstalled("core_content"));
        $this->assertTrue(isModuleInstalled("fortune2"));
    }

    public function testIsModuleInstalledReturnsFalse() {
        $this->assertFalse(isModuleInstalled("not_a_module"));
    }

}
