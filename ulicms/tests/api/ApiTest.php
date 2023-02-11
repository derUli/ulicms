<?php

use App\Models\Content\Language;
use App\Utils\CacheUtil;

class ApiTest extends \PHPUnit\Framework\TestCase
{
    private $initialUser;
    private $additionalMenus;
    private $initialSettings = [];

    protected function setUp(): void
    {
        $this->cleanUp();

        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $userQuery = Database::query("select id, html_editor "
                        . "from {prefix}users order by id asc limit 1", true);

        $this->initialUser = Database::fetchObject($userQuery);
        $this->additionalMenus = Settings::get("additional_menus");

        CacheUtil::clearCache();

        $avatarsDirectory = Path::resolve("ULICMS_ROOT/content/avatars/");
        if (!is_dir($avatarsDirectory)) {
            mkdir($avatarsDirectory, 0777, true);
        }

        $this->initialSettings = [
            "spamfilter_enabled" => Settings::get("spamfilter_enabled"),
            "min_time_to_fill_form" => Settings::get("min_time_to_fill_form")
        ];
    }

    protected function tearDown(): void
    {
        chdir(Path::resolve("ULICMS_ROOT"));

        foreach ($this->initialSettings as $key => $value) {
            Settings::set($key, $value);
        }

        Vars::setNoCache(false);

        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
        $this->cleanUp();
        Database::query("delete from {prefix}users where username like 'testuser-%'", true);

        $user = new User(
            intval($this->initialUser->id)
        );
        $user->setHtmlEditor($this->initialUser->html_editor);
        $user->save();
        Settings::set("additional_menus", $this->additionalMenus);

        $serverKeys = [
            'SERVER_PROTOCOL',
            'HTTP_HOST',
            'SERVER_PORT',
            'HTTPS',
            'REQUEST_URI',
            'slug'
        ];

        foreach ($serverKeys as $key) {
            if (isset($_SERVER[$key])) {
                unset($_SERVER[$key]);
            }
        }

        $_GET = [];
        $_POST = [];
    }

    public function cleanUp()
    {
        unset($_REQUEST["action"]);
        Settings::set('maintenance_mode', "0");
        chdir(Path::resolve("ULICMS_ROOT"));
    }

    public function testRemovePrefix()
    {
        $this->assertEquals("my_bar", remove_prefix("foo_my_bar", "foo_"));
        $this->assertEquals("my_foo_bar", remove_prefix("foo_my_foo_bar", "foo_"));
    }

    public function testRemoveSuffix()
    {
        $this->assertEquals("Hello", remove_suffix("Hello World!", " World!"));
        $this->assertEquals("Foo", remove_suffix("FooBar", "Bar"));
        $this->assertEquals("file", remove_suffix("file.txt", ".txt"));
        $this->assertEquals("FooBar", remove_suffix("FooBar", "Foo"));
        $this->assertEquals("", remove_suffix("Foo", "Foo"));
        $this->assertEquals("Foo", remove_suffix("Foo", "Hello"));
    }

    public function testGetAllUsedLanguages()
    {
        $languages = getAllUsedLanguages();
        $this->assertGreaterThanOrEqual(2, count($languages));
        $this->assertTrue(in_array('de', $languages));
        $this->assertTrue(in_array('en', $languages));
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

        $meta = getModuleMeta("Mobile_Detect");
        $this->assertIsArray($meta);
        $this->assertEquals("3.74.0", $meta["version"]);
        $this->assertEquals(false, $meta["embed"]);
    }

    public function testGetThemeMeta()
    {
        $meta = getThemeMeta("impro17");
        $this->assertIsArray($meta);
        $this->assertEquals("2.1.5", $meta["version"]);
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
        $this->assertEquals("image/png", get_mime(Path::resolve("ULICMS_ROOT/admin/gfx/edit.png")));
    }

    public function testGetActionIsSet()
    {
        $_REQUEST["action"] = "pages";
        $this->assertEquals("pages", get_action());
        unset($_REQUEST["action"]);
    }

    public function testGetActionIsNotSet()
    {
        $this->assertEquals("home", get_action());
    }

    public function testGetStringLengthInBytes()
    {
        $this->assertEquals(39, getStringLengthInBytes("Das ist die Lösung für die Änderung."));
    }

    public function testCheckFormTimestampReturnsTrue()
    {
        Settings::set("min_time_to_fill_form", 3);
        $_POST["form_timestamp"] = time() - 4;
        $this->assertTrue(_check_form_timestamp());
    }

    public function testCheckFormTimestampReturnsFalse()
    {
        Settings::set("min_time_to_fill_form", 3);
        $_POST["form_timestamp"] = time() - 1;
        $this->assertFalse(_check_form_timestamp());
    }

    public function testGetJqueryUrl()
    {
        $this->assertEquals("node_modules/jquery/dist/jquery.min.js", get_jquery_url());
    }

    public function testRandStr()
    {
        $password1 = rand_string(15);
        $password2 = rand_string(15);
        $password3 = rand_string(12);
        $this->assertEquals(15, strlen($password1));
        $this->assertEquals(15, strlen($password2));
        $this->assertEquals(12, strlen($password3));
        $this->assertNotEquals($password2, $password1);
    }

    public function testSplitAndTrim()
    {
        $input = "Max;
        Muster;
        max@muster.de;
        Musterstadt";
        $result = splitAndTrim($input);
        $this->assertEquals("Max", $result[0]);
        $this->assertEquals("Muster", $result[1]);
        $this->assertEquals("max@muster.de", $result[2]);
        $this->assertEquals("Musterstadt", $result[3]);
    }

    public function testGetAllThemes()
    {
        $themes = getAllThemes();
        $this->assertContains("impro17", $themes);
        $this->assertContains("2020", $themes);
    }

    public function testGetAllModules()
    {
        Vars::delete("allModules");
        $modules = getAllModules();
        $modules = getAllModules();
        $this->assertContains("core_content", $modules);
        $this->assertContains("slicknav", $modules);
        $this->assertContains("bootstrap", $modules);
    }

    public function testGetPreferredLanguage()
    {
        $acceptLanguageHeader1 = "Accept-Language: da, en - gb;
        q = 0.8, en;
        q = 0.7, de;
        q = 0.5";
        $this->assertEquals('en', get_prefered_language(array('de', 'en'), $acceptLanguageHeader1));

        $acceptLanguageHeader2 = "Accept-Language: da, en - gb;
        q = 0.8, en;
        q = 0.7, de;
        q = 0.9";
        $this->assertEquals('de', get_prefered_language(array('de', 'en'), $acceptLanguageHeader2));
    }

    public function testGetHtmlEditorNotLoggedInReturnsCkeditor()
    {
        if (session_id()) {
        }
        $this->assertEquals("ckeditor", get_html_editor());
    }

    public function testGetHtmlEditorReturnsCKEditor()
    {
        $user = new User();
        $user->setUsername("testuser-1");
        $user->setPassword(rand_string(23));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
        $user->setHTMLEditor("ckeditor");
        $user->save();

        register_session(getUserByName(("testuser-1")), false);
        $this->assertEquals("ckeditor", get_html_editor());
    }

    public function testGetHtmlEditorReturnsCodeMirror()
    {
        $user = new User();
        $user->setUsername("testuser-2");
        $user->setPassword(rand_string(666));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
        $user->setHTMLEditor("codemirror");
        $user->save();

        register_session(getUserByName(("testuser-2")), false);
        $this->assertEquals("codemirror", get_html_editor());
    }

    public function testIdefine()
    {
        $this->assertFalse(defined("test_hello"));

        $this->assertTrue(idefine("TEST_HELLO", "World"));
        $this->assertEquals("World", TEST_HELLO);

        $this->assertFalse(idefine("TEST_HELLO", "Uli"));
        $this->assertEquals("World", TEST_HELLO);
    }

    public function testGetAllUsedMenus()
    {
        $menus = get_all_used_menus();
        $this->assertCount(1, $menus);
        $this->isTrue(in_array("top", $menus));
        $this->isFalse(in_array("left", $menus));
    }

    public function testCmsVersion()
    {
        $this->assertTrue(\App\Utils\VersionComparison\compare(
            cms_version(),
            "2019.4",
            ">"
        ));
    }

    public function testGetEnvironment()
    {
        $this->assertEquals("test", get_environment());
    }

    public function testFuncEnabledReturnsTrue()
    {
        $enabled = func_enabled("mysqli_connect");
        $this->assertTrue($enabled);
    }

    public function testGetBaseFolderUrlWithFilenameInUrl()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "80";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo";

        $this->assertEquals("http://example.org/foobar", getBaseFolderURL());
    }

    public function testGetBaseFolderUrlWithFilenameInUrlAndHttps()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "443";
        $_SERVER['HTTPS'] = "on";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo";

        $this->assertEquals("https://example.org/foobar", getBaseFolderURL());

        unset($_SERVER['SERVER_PROTOCOL']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['HTTPS']);
    }

    public function testGetBaseFolderUrlWithFilenameInUrlAndHttpsAndAlternativePort()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "8080";
        $_SERVER['HTTPS'] = "on";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo";

        $this->assertEquals("https://example.org:8080/foobar", getBaseFolderURL());

        unset($_SERVER['SERVER_PROTOCOL']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['HTTPS']);
    }

    public function testGetBaseFolderUrlWithoutFilename()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "80";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/";

        $this->assertEquals("http://example.org/foobar", getBaseFolderURL());

        unset($_SERVER['SERVER_PROTOCOL']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['REQUEST_URI']);
    }

    public function testGetCurrentURL()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "8080";
        $_SERVER['HTTPS'] = "on";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo?hello=world";

        $this->assertEquals("https://example.org:8080/foobar/foo?hello=world", getCurrentURL());

        unset($_SERVER['SERVER_PROTOCOL']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['HTTPS']);
    }

    public function testStringContainsShortCodeWithoutNameReturnsTrue()
    {
        $this->assertTrue(
            stringContainsShortCodes(
                'Foo [module=hello_world] Bar'
            )
        );
        $this->assertTrue(
            stringContainsShortCodes(
                'Foo [module="hello_world"] Bar'
            )
        );
    }

    public function testStringContainsShortCodeWithoutNameReturnsFalse()
    {
        $this->assertFalse(
            stringContainsShortCodes(
                '[module=hello_world '
            )
        );
        $this->assertFalse(
            stringContainsShortCodes(
                'nic-code'
            )
        );
    }

    public function testStringContainsShortCodeWithNameReturnsTrue()
    {
        $this->assertTrue(
            stringContainsShortCodes(
                'Foo [module=hello_world] Bar',
                'hello_world'
            )
        );
        $this->assertTrue(
            stringContainsShortCodes(
                'Foo [module="hello_world"] Bar',
                'hello_world'
            )
        );
    }

    public function testStringContainsShortCodeWithNameReturnsFalse()
    {
        $this->assertFalse(
            stringContainsShortCodes(
                'Foo [module="hello_world"] Bar',
                'berlin'
            )
        );
        $this->assertFalse(
            stringContainsShortCodes(
                'Foo [module=hello_world] Bar',
                'berlin'
            )
        );
    }

    public function testReplaceShortcodesWithModulesWithOther()
    {
        $inputString = 'Foo [year] Bar [module=fortune2]';
        $processedInput = replaceShortcodesWithModules($inputString, true);

        $this->assertStringStartsWith(
            'Foo ' . date("Y") . ' Bar ',
            $processedInput
        );
        $this->assertStringEndsNotWith(
            '[module=fortune2]',
            $processedInput
        );
        $this->assertGreaterThan(
            strlen($inputString) + 10,
            strlen($processedInput)
        );
    }

    public function testReplaceShortcodesWithModulesThreeFormats()
    {
        $formats = [
            '[module=fortune2]',
            '[module="fortune2"]',
            '[module=&quot;fortune2&quot;]'
        ];
        foreach ($formats as $format) {
            $html = replaceShortcodesWithModules($format, false);
            $this->assertNotEquals($format, $html);
            $this->assertGreaterThan(strlen($format), strlen($html));
        }
    }

    public function testReplaceShortcodesWithNonExistingName()
    {
        $this->assertEquals(
            '[module=gibts_nicht]',
            replaceShortcodesWithModules('[module=gibts_nicht]')
        );
    }

    public function testReplaceShortcodesWithModulesWithoutOther()
    {
        $inputString = 'Foo [year] Bar [module=fortune2]';
        $processedInput = replaceShortcodesWithModules($inputString, false);

        $this->assertStringStartsWith('Foo [year] Bar ', $processedInput);
        $this->assertStringEndsNotWith('[module=fortune2]', $processedInput);
        $this->assertGreaterThan(strlen($inputString) + 10, strlen($processedInput));
    }

    public function testReplaceOtherShortcodes()
    {
        $this->assertStringMatchesFormat('Foo %d Bar [module=fortune2]', replaceOtherShortCodes('Foo [year] Bar [module=fortune2]'));
    }

    public function testContainsModuleWithoutArgumentsReturnsTrue()
    {
        $page = new Module_Page();
        $page->title = "Unit Test " . uniqid();
        $page->slug = "unit-test-" . uniqid();
        $page->menu = "none";
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->module = "fortune2";
        $page->content = "Hello World";
        $page->save();

        $_SESSION['language'] = 'de';
        $_GET["slug"] = $page->slug;
        $this->assertTrue(containsModule());
    }

    public function testContainsModuleReturnsTrue()
    {
        $page = new Module_Page();
        $page->title = "Unit Test " . uniqid();
        $page->slug = "unit-test-" . uniqid();
        $page->menu = "none";
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->module = "fortune2";
        $page->content = "Hello World";
        $page->save();

        $_SESSION['language'] = 'de';
        $this->assertTrue(containsModule($page->slug));
    }

    public function testContainsModuleReturnsFalse()
    {
        $page = new Page();
        $page->title = "Unit Test " . uniqid();
        $page->slug = "unit-test-" . uniqid();
        $page->menu = "none";
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $_SESSION['language'] = 'de';

        $this->assertFalse(containsModule($page->slug));
    }

    public function testContainsModuleWithModulePageAndNameReturnsTrue()
    {
        $page = new Module_Page();
        $page->title = "Unit Test " . uniqid();
        $page->slug = "unit-test-" . uniqid();
        $page->menu = "none";
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->module = "fortune2";
        $page->content = "Hello World";
        $page->save();

        $_SESSION['language'] = 'de';
        $this->assertTrue(containsModule($page->slug, "fortune2"));
    }

    public function testContainsModuleWithShortcodeAndNameReturnsTrue()
    {
        $page = new Page();
        $page->title = "Unit Test " . uniqid();
        $page->slug = "unit-test-" . uniqid();
        $page->menu = "none";
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->content = "Hello [module=fortune2] World";
        $page->save();

        $_SESSION['language'] = 'de';
        $this->assertTrue(containsModule($page->slug, "fortune2"));
    }

    public function testContainsModuleWithNameReturnsFalse()
    {
        $page = new Page();
        $page->title = "Unit Test " . uniqid();
        $page->slug = "unit-test-" . uniqid();
        $page->menu = "none";
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->content = "Hello [module=fortune2] World";
        $page->save();

        $_SESSION['language'] = 'de';

        $this->assertFalse(containsModule($page->slug, "nicht_enthalten"));
    }

    public function testBuildSEOUrlWithoutAnythingNoPageSpecified()
    {
        unset($_GET["slug"]);
        unset($_GET["html"]);

        $this->assertEquals("./", buildSEOUrl());
    }

    public function testBuildSEOUrlWithoutAnything()
    {
        set_requested_pagename("hello_world");
        $this->assertEquals("hello_world", buildSEOUrl());
    }

    public function testBuildSEOUrlWithPage()
    {
        $this->assertEquals("foobar", buildSEOUrl("foobar"));
    }

    public function testBuildSEOUrlWithPageAndRedirection()
    {
        $this->assertEquals("#", buildSEOUrl("foobar", "#"));

        $this->assertEquals("https://google.com", buildSEOUrl("foobar", "https://google.com"));
    }

    public function testBuildSEOUrlWithPageAndType()
    {
        $this->assertEquals(
            "foobar",
            buildSEOUrl("foobar", null)
        );
    }

    public function testGetAllLanguagesFiltered()
    {
        $language = new Language();
        $language->loadByLanguageCode('en');

        $group = new Group();
        $group->setName("Testgroup");
        $group->setLanguages(
            [
                $language
            ]
        );
        $group->save();

        $user = new User();
        $user->setUsername("testuser-1");
        $user->setPassword(rand_string(23));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
        $user->setHTMLEditor("ckeditor");
        $user->setPrimaryGroup($group);
        $user->save();

        register_session(
            getUserById($user->getId())
        );
        $languages = getAllLanguages(true);

        $this->assertNotContains('de', $languages);
        $this->assertContains('en', $languages);
        $user->delete();
        $group->delete();
    }

    public function testGetAllLanguagesNotFiltered()
    {
        $languages = getAllLanguages();
        $this->assertGreaterThanOrEqual(1, count($languages));
    }

    public function testVarDumpStrReturnsStringWithOneVar()
    {
        $output = var_dump_str(new User());

        $this->assertStringContainsString(
            "User",
            normalizeLN($output)
        );
        $this->assertStringContainsString(
            "id",
            $output
        );
        $this->assertStringContainsString(
            "firstname",
            $output
        );
        $this->assertStringContainsString(
            "lastname",
            $output
        );
        $this->assertStringContainsString(
            "secondary_groups",
            $output
        );
        $this->assertStringContainsString(
            "NULL",
            $output
        );
    }

    public function testVarDumpStrWithoutAnything()
    {
        $this->assertEmpty(var_dump_str());
    }

    public function testGetAllMenus()
    {
        $menus = getAllMenus();
        $this->assertContains("top", $menus);
        $this->assertContains("not_in_menu", $menus);
        $this->assertNotContains("foo", $menus);
        $this->assertNotContains("bar", $menus);
    }

    public function testGetAllMenusWithAdditional()
    {
        Settings::set("additional_menus", "foo;bar");

        $menus = getAllMenus(false, false);

        $this->assertContains("top", $menus);
        $this->assertContains("not_in_menu", $menus);
        $this->assertContains("foo", $menus);
        $this->assertContains("bar", $menus);

        getAllMenus();
    }

    public function testGetAllMenusWithAdditionalOnlyUsed()
    {
        Settings::set("additional_menus", "foo;bar");

        $menus = getAllMenus(true, false);
        $this->assertContains("top", $menus);
        $this->assertContains("not_in_menu", $menus);
        $this->assertNotContains("foo", $menus);
        $this->assertNotContains("bar", $menus);
    }

    public function testGetLangConfig()
    {
        Settings::setLanguageSetting("my_setting", "Lampukisch");
        Settings::setLanguageSetting("my_setting", "Germanisch", 'de');
        Settings::setLanguageSetting("my_setting", "Angelsächisch", 'en');

        $this->assertEquals("Lampukisch", get_lang_config("my_setting", "fr"));
        $this->assertEquals("Germanisch", get_lang_config("my_setting", 'de'));
        $this->assertEquals("Angelsächisch", get_lang_config("my_setting", 'en'));
    }

    public function testGetUsedPostTypes()
    {
        $postTypes = get_used_post_types();
        $this->assertContains("page", $postTypes);
    }

    public function testGetLanguageNameByCodeReturnsName()
    {
        $this->assertEquals("Deutsch", getLanguageNameByCode('de'));
        $this->assertEquals("English", getLanguageNameByCode('en'));
    }

    public function testGetLanguageNameByCodeReturnsCode()
    {
        $this->assertEquals(
            "gibts_nicht",
            getLanguageNameByCode("gibts_nicht")
        );
    }

    public function testGetAvailableBackendLanguages()
    {
        $this->assertContains('de', getAvailableBackendLanguages());
        $this->assertContains('en', getAvailableBackendLanguages());
    }

    public function testJsonReadableEncode()
    {
        $data = [
            "foo" => "bar",
            "hello" => "world",
            "animals" => ["cat", "dog", "pig"],
            "number" => 123,
            "boolean" => true,
            "null" => null
        ];
        $expected = file_get_contents(
            Path::resolve(
                "ULICMS_ROOT/tests/fixtures/json_readable_encode.txt"
            )
        );
        $output = json_readable_encode($data);

        $this->assertEquals(
            normalizeLN($expected),
            normalizeLN($output)
        );
    }

    public function testGetSystemLanguageReturnsSystemLanguageFromSession()
    {
        $_SESSION["system_language"] = 'de';
        $_SESSION['language'] = 'en';
        $this->assertEquals('de', getSystemLanguage());
    }

    public function testGetSystemLanguageReturnsFrontendLanguageFromSession()
    {
        $_SESSION['language'] = 'en';
        $this->assertEquals('en', getSystemLanguage());
    }

    public function testGetSystemLanguageReturnsSystemLanguageFromSetting()
    {
        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }
        }

        $system_language = Settings::get("system_language");
        Settings::set("system_language", 'en');
        $this->assertEquals('en', getSystemLanguage());

        Settings::set("system_language", $system_language);
    }

    public function testGetSystemLanguageReturnsDe()
    {
        $system_language = Settings::get("system_language");

        Settings::delete("system_language");

        $this->assertEquals('de', getSystemLanguage());

        Settings::set("system_language", $system_language);
    }

    public function testGetModuleUninstallScriptPath()
    {
        $this->assertStringEndsWith("content/modules/my_module/my_module_uninstall.php", getModuleUninstallScriptPath("my_module"));
    }

    public function testGetModuleUninstallScriptPath2()
    {
        $this->assertStringEndsWith("content/modules/my_module/uninstall.php", getModuleUninstallScriptPath2("my_module"));
    }

    public function testGetFieldsForCustomType()
    {
        $this->assertCount(0, getFieldsForCustomType("gibts_nicht"));
    }

    public function testGetShortlink()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "443";
        $_SERVER['HTTPS'] = "on";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo";

        $pages = ContentFactory::getAll();

        $expected = '/?goid=' . $pages[0]->getId();
        $shortlink = get_shortlink($pages[0]->getId());

        $this->assertEquals(
            "https://example.org/foobar/?goid=1",
            $shortlink
        );
    }

    public function testGetCanonical()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = "443";
        $_SERVER['HTTPS'] = "on";
        $_SERVER['HTTP_HOST'] = "example.org";
        $_SERVER['REQUEST_URI'] = "/foobar/foo";

        $_GET["slug"] = "hello_world";

        $this->assertEquals(
            "https://example.org/foobar/hello_world",
            get_canonical()
        );
    }

    // XXX: Whats the purpose of this method?
    public function testGetModuleAdminSelfPath()
    {
        $_SERVER['REQUEST_URI'] = "/foo/?bar=\"hello\"";
        $this->assertEquals("/foo/?bar=&quot;hello&quot;", getModuleAdminSelfPath());
    }

    public function testRootDirectory()
    {
        $_SERVER['HTTP_HOST'] = "company.com";
        $_SERVER['REQUEST_URI'] = "/subdir/foo.png";

        $this->assertEquals("http://company.com/subdir/", rootDirectory());
    }

    public function testGetModuleAdminFilePath()
    {
        $this->assertStringEndsWith(
            "/content/modules/my_module/my_module_admin.php",
            getModuleAdminFilePath("my_module")
        );
    }

    public function testGetModuleAdminFilePath2()
    {
        $this->assertStringEndsWith(
            "/content/modules/my_module/admin.php",
            getModuleAdminFilePath2("my_module")
        );
    }

    public function testNoCacheWithTrue()
    {
        $this->assertFalse(Vars::getNoCache());

        no_cache(true);

        $this->assertTrue(Vars::getNoCache());
    }
}
