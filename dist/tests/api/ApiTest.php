<?php

use App\Models\Content\Language;
use App\Utils\CacheUtil;
use App\Constants\HtmlEditor;
use App\Helpers\TestHelper;

class ApiTest extends \PHPUnit\Framework\TestCase
{
    private $initialUser;
    private $initialSettings = [];

    protected function setUp(): void
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();
        ControllerRegistry::loadModuleControllers();

        $this->cleanUp();

        $userQuery = Database::query("select id, html_editor "
                        . "from {prefix}users order by id asc limit 1", true);

        $this->initialUser = Database::fetchObject($userQuery);

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

        $meta = getModuleMeta("bootstrap");
        $this->assertIsArray($meta);
        $this->assertEquals("3.3.7", $meta["version"]);
        $this->assertEquals(false, $meta["embed"]);
    }

    public function testGetJqueryUrl()
    {
        $this->assertEquals("node_modules/jquery/dist/jquery.min.js", get_jquery_url());
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
        $this->assertEquals(HtmlEditor::CKEDITOR, get_html_editor());
    }

    public function testGetHtmlEditorReturnsCKEditor()
    {
        $user = new User();
        $user->setUsername("testuser-1");
        $user->setPassword(rand_string(23));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
        $user->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user->save();

        register_session(getUserByName(("testuser-1")), false);
        $this->assertEquals(HtmlEditor::CKEDITOR, get_html_editor());
    }

    public function testGetHtmlEditorReturnsCodeMirror()
    {
        $user = new User();
        $user->setUsername("testuser-2");
        $user->setPassword(rand_string(666));
        $user->setLastname("Beutlin");
        $user->setFirstname("Bilbo");
        $user->setHTMLEditor(HtmlEditor::CODEMIRROR);
        $user->save();

        register_session(getUserByName(("testuser-2")), false);
        $this->assertEquals(HtmlEditor::CODEMIRROR, get_html_editor());
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
        $user->setHTMLEditor(HtmlEditor::CKEDITOR);
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

    public function testGetLangConfig()
    {
        Settings::setLanguageSetting("my_setting", "Lampukisch");
        Settings::setLanguageSetting("my_setting", "Germanisch", 'de');
        Settings::setLanguageSetting("my_setting", "Angelsächisch", 'en');

        $this->assertEquals("Lampukisch", Settings::getLang("my_setting", "fr"));
        $this->assertEquals("Germanisch", Settings::getLang("my_setting", 'de'));
        $this->assertEquals("Angelsächisch", Settings::getLang("my_setting", 'en'));
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
        Vars::setNoCache(true);
        $this->assertTrue(Vars::getNoCache());
    }
}
