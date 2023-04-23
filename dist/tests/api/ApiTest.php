<?php

use App\Constants\HtmlEditor;
use App\Utils\CacheUtil;

class ApiTest extends \PHPUnit\Framework\TestCase {
    private $initialUser;

    private $initialSettings = [];

    protected function setUp(): void {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();
        ControllerRegistry::loadModuleControllers();

        $this->cleanUp();

        $userQuery = Database::query('select id, html_editor '
                        . 'from {prefix}users order by id asc limit 1', true);

        $this->initialUser = Database::fetchObject($userQuery);

        CacheUtil::clearCache();

        $avatarsDirectory = Path::resolve('ULICMS_ROOT/content/avatars/');
        if (! is_dir($avatarsDirectory)) {
            mkdir($avatarsDirectory, 0777, true);
        }

        $this->initialSettings = [
            'spamfilter_enabled' => Settings::get('spamfilter_enabled'),
            'min_time_to_fill_form' => Settings::get('min_time_to_fill_form')
        ];
    }

    protected function tearDown(): void {
        chdir(Path::resolve('ULICMS_ROOT'));

        foreach ($this->initialSettings as $key => $value) {
            Settings::set($key, $value);
        }

        \App\Storages\Vars::setNoCache(false);

        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
        $this->cleanUp();
        Database::query("delete from {prefix}users where username like 'testuser-%'", true);

        $user = new User((int)$this->initialUser->id);
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

    public function cleanUp() {
        unset($_REQUEST['action']);
        Settings::set('maintenance_mode', '0');
        chdir(Path::resolve('ULICMS_ROOT'));
    }

    public function testAddTranslation() {
        $key1 = uniqid();
        $key2 = 'TRANSLATION_' . uniqid();
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
        $this->assertEquals('core', getModuleMeta('core_home', 'source'));
        $meta = getModuleMeta('core_home');
        $this->assertEquals('models/HomeViewModel.php', $meta['objects']['HomeViewModel']);
        $this->assertFalse($meta['embed']);
        $this->assertNull(getModuleMeta('not_a_module'));
        $this->assertNull(getModuleMeta('not_a_module', 'version'));
        $this->assertNull(getModuleMeta('core_home', 'not_here'));

        $meta = getModuleMeta('bootstrap');
        $this->assertIsArray($meta);
        $this->assertEquals('3.3.7', $meta['version']);
        $this->assertEquals(false, $meta['embed']);
    }

    public function testGetAllThemes() {
        $themes = getAllThemes();
        $this->assertContains('impro17', $themes);
        $this->assertContains('2020', $themes);
    }

    public function testGetAllModules() {
        \App\Storages\Vars::delete('allModules');
        $modules = getAllModules();
        $modules = getAllModules();
        $this->assertContains('core_content', $modules);
        $this->assertContains('slicknav', $modules);
        $this->assertContains('bootstrap', $modules);
    }

    public function testGetHtmlEditorNotLoggedInReturnsCkeditor() {
        if (session_id()) {
        }
        $this->assertEquals(HtmlEditor::CKEDITOR, get_html_editor());
    }

    public function testGetHtmlEditorReturnsCKEditor() {
        $user = new User();
        $user->setUsername('testuser-1');
        $user->setPassword(rand_string(23));
        $user->setLastname('Beutlin');
        $user->setFirstname('Bilbo');
        $user->setHTMLEditor(HtmlEditor::CKEDITOR);
        $user->save();

        register_session(getUserByName(('testuser-1')), false);
        $this->assertEquals(HtmlEditor::CKEDITOR, get_html_editor());
    }

    public function testGetHtmlEditorReturnsCodeMirror() {
        $user = new User();
        $user->setUsername('testuser-2');
        $user->setPassword(rand_string(666));
        $user->setLastname('Beutlin');
        $user->setFirstname('Bilbo');
        $user->setHTMLEditor(HtmlEditor::CODEMIRROR);
        $user->save();

        register_session(getUserByName(('testuser-2')), false);
        $this->assertEquals(HtmlEditor::CODEMIRROR, get_html_editor());
    }

    public function testGetBaseFolderUrlWithFilenameInUrl() {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo';

        $this->assertEquals('http://example.org/foobar', getBaseFolderURL());
    }

    public function testGetBaseFolderUrlWithFilenameInUrlAndHttps() {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '443';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo';

        $this->assertEquals('https://example.org/foobar', getBaseFolderURL());

        unset($_SERVER['SERVER_PROTOCOL'], $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $_SERVER['REQUEST_URI'], $_SERVER['HTTPS']);




    }

    public function testGetBaseFolderUrlWithFilenameInUrlAndHttpsAndAlternativePort() {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/foobar/foo';

        $this->assertEquals('https://example.org:8080/foobar', getBaseFolderURL());

        unset($_SERVER['SERVER_PROTOCOL'], $_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $_SERVER['REQUEST_URI'], $_SERVER['HTTPS']);




    }

    public function testStringContainsShortCodeWithoutNameReturnsTrue() {
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

    public function testStringContainsShortCodeWithoutNameReturnsFalse() {
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

    public function testStringContainsShortCodeWithNameReturnsTrue() {
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

    public function testStringContainsShortCodeWithNameReturnsFalse() {
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

    public function testReplaceShortcodesWithModulesWithOther() {
        $inputString = 'Foo [year] Bar [module=fortune2]';
        $processedInput = replaceShortcodesWithModules($inputString, true);

        $this->assertStringStartsWith(
            'Foo ' . date('Y') . ' Bar ',
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

    public function testReplaceShortcodesWithModulesThreeFormats() {
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

    public function testReplaceShortcodesWithNonExistingName() {
        $this->assertEquals(
            '[module=gibts_nicht]',
            replaceShortcodesWithModules('[module=gibts_nicht]')
        );
    }

    public function testReplaceShortcodesWithModulesWithoutOther() {
        $inputString = 'Foo [year] Bar [module=fortune2]';
        $processedInput = replaceShortcodesWithModules($inputString, false);

        $this->assertStringStartsWith('Foo [year] Bar ', $processedInput);
        $this->assertStringEndsNotWith('[module=fortune2]', $processedInput);
        $this->assertGreaterThan(strlen($inputString) + 10, strlen($processedInput));
    }

    public function testReplaceOtherShortcodes() {
        $this->assertStringMatchesFormat('Foo %d Bar [module=fortune2]', replaceOtherShortCodes('Foo [year] Bar [module=fortune2]'));
    }

    public function testContainsModuleWithoutArgumentsReturnsTrue() {
        $page = new Module_Page();
        $page->title = 'Unit Test ' . uniqid();
        $page->slug = 'unit-test-' . uniqid();
        $page->menu = 'none';
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->module = 'fortune2';
        $page->content = 'Hello World';
        $page->save();

        $_SESSION['language'] = 'de';
        $_GET['slug'] = $page->slug;
        $this->assertTrue(containsModule());
    }

    public function testContainsModuleReturnsTrue() {
        $page = new Module_Page();
        $page->title = 'Unit Test ' . uniqid();
        $page->slug = 'unit-test-' . uniqid();
        $page->menu = 'none';
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->module = 'fortune2';
        $page->content = 'Hello World';
        $page->save();

        $_SESSION['language'] = 'de';
        $this->assertTrue(containsModule($page->slug));
    }

    public function testContainsModuleReturnsFalse() {
        $page = new Page();
        $page->title = 'Unit Test ' . uniqid();
        $page->slug = 'unit-test-' . uniqid();
        $page->menu = 'none';
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->save();

        $_SESSION['language'] = 'de';

        $this->assertFalse(containsModule($page->slug));
    }

    public function testContainsModuleWithModulePageAndNameReturnsTrue() {
        $page = new Module_Page();
        $page->title = 'Unit Test ' . uniqid();
        $page->slug = 'unit-test-' . uniqid();
        $page->menu = 'none';
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->module = 'fortune2';
        $page->content = 'Hello World';
        $page->save();

        $_SESSION['language'] = 'de';
        $this->assertTrue(containsModule($page->slug, 'fortune2'));
    }

    public function testContainsModuleWithShortcodeAndNameReturnsTrue() {
        $page = new Page();
        $page->title = 'Unit Test ' . uniqid();
        $page->slug = 'unit-test-' . uniqid();
        $page->menu = 'none';
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->content = 'Hello [module=fortune2] World';
        $page->save();

        $_SESSION['language'] = 'de';
        $this->assertTrue(containsModule($page->slug, 'fortune2'));
    }

    public function testContainsModuleWithNameReturnsFalse() {
        $page = new Page();
        $page->title = 'Unit Test ' . uniqid();
        $page->slug = 'unit-test-' . uniqid();
        $page->menu = 'none';
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->content = 'Hello [module=fortune2] World';
        $page->save();

        $_SESSION['language'] = 'de';

        $this->assertFalse(containsModule($page->slug, 'nicht_enthalten'));
    }

    public function testVarDumpStrReturnsStringWithOneVar() {
        $output = var_dump_str(new User());

        $this->assertStringContainsString(
            'User',
            normalizeLN($output)
        );
        $this->assertStringContainsString(
            'id',
            $output
        );
        $this->assertStringContainsString(
            'firstname',
            $output
        );
        $this->assertStringContainsString(
            'lastname',
            $output
        );
        $this->assertStringContainsString(
            'secondary_groups',
            $output
        );
        $this->assertStringContainsString(
            'NULL',
            $output
        );
    }

    public function testVarDumpStrWithoutAnything() {
        $this->assertEmpty(var_dump_str());
    }

    public function testGetLangConfig() {
        Settings::setLanguageSetting('my_setting', 'Lampukisch');
        Settings::setLanguageSetting('my_setting', 'Germanisch', 'de');
        Settings::setLanguageSetting('my_setting', 'Angelsächisch', 'en');

        $this->assertEquals('Lampukisch', Settings::getLang('my_setting', 'fr'));
        $this->assertEquals('Germanisch', Settings::getLang('my_setting', 'de'));
        $this->assertEquals('Angelsächisch', Settings::getLang('my_setting', 'en'));
    }

    public function testGetModuleUninstallScriptPath() {
        $this->assertStringEndsWith('content/modules/my_module/my_module_uninstall.php', getModuleUninstallScriptPath('my_module'));
    }

    public function testGetModuleUninstallScriptPath2() {
        $this->assertStringEndsWith('content/modules/my_module/uninstall.php', getModuleUninstallScriptPath2('my_module'));
    }

    // XXX: Whats the purpose of this method?
    public function testGetModuleAdminSelfPath() {
        $_SERVER['REQUEST_URI'] = '/foo/?bar="hello"';
        $this->assertEquals('/foo/?bar=&quot;hello&quot;', getModuleAdminSelfPath());
    }

    public function testGetModuleAdminFilePath() {
        $this->assertStringEndsWith(
            '/content/modules/my_module/my_module_admin.php',
            getModuleAdminFilePath('my_module')
        );
    }

    public function testGetModuleAdminFilePath2() {
        $this->assertStringEndsWith(
            '/content/modules/my_module/admin.php',
            getModuleAdminFilePath2('my_module')
        );
    }

    public function testNoCacheWithTrue() {
        $this->assertFalse(\App\Storages\Vars::getNoCache());
        \App\Storages\Vars::setNoCache(true);
        $this->assertTrue(\App\Storages\Vars::getNoCache());
    }
}
