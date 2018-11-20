<?php
use UliCMS\Exceptions\NotImplementedException;

class PageTest extends \PHPUnit\Framework\TestCase
{

    private $user;

    private $commentsInitialEnabled;

    private $initialCommentableContentTypes;

    public function setUp()
    {
        @session_start();
        
        $manager = new UserManager();
        $users = $manager->getAllUsers();
        $this->user = $users[0];
        
        $this->commentsInitialEnabled = Settings::get("comments_enabled");
        $this->initialCommentableContentTypes = Settings::get("commentable_content_types");
        
        $this->cleanUp();
    }

    public function tearDown()
    {
        @session_destroy();
        $this->cleanUp();
        
        if ($this->commentsInitialEnabled) {
            Settings::set("comments_enabled", "1");
        } else {
            Settings::delete("comments_enabled");
        }
        if ($this->initialCommentableContentTypes) {
            Settings::set("commentable_content_types", $this->initialCommentableContentTypes);
        } else {
            Settings::delete("commentable_content_types");
        }
    }

    private function cleanUp()
    {
        Database::query("delete from {prefix}content where systemname = 'testdisableshortcodes' or title like 'Unit Test%'", true);
        
        Settings::delete("comments_enabled");
        Settings::delete("commentable_content_types");
    }

    private $ipsum = 'Lorem ipsum dolor sit amet,
		[module="fortune2"]
		[module="test"]
		[module=&quot;hello&quot;]
		consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';

    public function testGetEmbeddedModulesPage()
    {
        $page = new Page();
        $page->content = $this->ipsum;
        $this->assertEquals(3, count($page->getEmbeddedModules()));
    }

    public function testGetEmbeddedModulesModulePage()
    {
        $page = new Module_Page();
        
        $page->content = $this->ipsum;
        $this->assertEquals(3, count($page->getEmbeddedModules()));
        $page->module = "fortune2";
        $this->assertEquals(3, count($page->getEmbeddedModules()));
        $page->module = "pfbc_sample";
        $this->assertEquals(4, count($page->getEmbeddedModules()));
    }

    public function testDisableShortcodesTrue()
    {
        $page = new Page();
        $page->title = 'testDisableShortcodesTrue';
        $page->systemname = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->autor = 1;
        $page->group_id = 1;
        $page->custom_data["disable_shortcodes"] = true;
        $page->save();
        
        $_SESSION["language"] = 'de';
        $_GET["seite"] = "testdisableshortcodes";
        
        $this->assertFalse(str_contains(get_csrf_token_html(), get_content()));
        $this->assertTrue(str_contains("[csrf_token_html]", get_content()));
        
        $this->cleanUp();
        
        unset($_SESSION["language"]);
        unset($_GET["seite"]);
    }

    public function testDisableShortcodesFalse()
    {
        $page = new Page();
        $page->title = 'testDisableShortcodesFalse';
        $page->systemname = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->autor = 1;
        $page->group_id = 1;
        $page->custom_data["disable_shortcodes"] = false;
        $page->save();
        
        $_SESSION["language"] = 'de';
        $_GET["seite"] = "testdisableshortcodes";
        
        $this->assertTrue(str_contains(get_csrf_token_html(), get_content()));
        $this->assertFalse(str_contains("[csrf_token_html]", get_content()));
        
        $this->cleanUp();
        
        unset($_SESSION["language"]);
        unset($_GET["seite"]);
    }

    public function testDisableShortcodesNull()
    {
        $page = new Page();
        $page->title = 'testDisableShortcodesNull';
        $page->systemname = 'testdisableshortcodes';
        $page->language = 'de';
        $page->content = "foo [csrf_token_html] bar";
        $page->autor = 1;
        $page->group_id = 1;
        $page->autor = 1;
        $page->group_id = 1;
        $page->save();
        
        $_SESSION["language"] = 'de';
        $_GET["seite"] = "testdisableshortcodes";
        
        $this->assertTrue(str_contains(get_csrf_token_html(), get_content()));
        $this->assertFalse(str_contains("[csrf_token_html]", get_content()));
        $this->cleanUp();
        
        unset($_SESSION["language"]);
        unset($_GET["seite"]);
    }

    public function testCreatePageWithCommentsEnabledTrue()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->systemname = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->autor = 1;
        $page->group_id = 1;
        $page->save();
        
        $this->assertNotNull($page->id);
        
        $page = new Page($page->id);
        $this->assertTrue($page->comments_enabled);
        
        $this->cleanUp();
    }

    public function testCreatePageWithCommentsEnabledFalse()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->systemname = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = false;
        $page->autor = 1;
        $page->group_id = 1;
        $page->save();
        
        $this->assertNotNull($page->id);
        
        $page = new Page($page->id);
        $this->assertFalse($page->comments_enabled);
        
        $this->cleanUp();
    }

    public function testCreatePageWithCommentsEnabledNull()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->systemname = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = null;
        $page->autor = 1;
        $page->group_id = 1;
        $page->save();
        
        $this->assertNotNull($page->id);
        
        $page = new Page($page->id);
        $this->assertNull($page->comments_enabled);
        
        $this->cleanUp();
    }

    public function testUpdatePageWithCommentsEnabledTrue()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->systemname = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->autor = 1;
        $page->group_id = 1;
        $page->save();
        
        $this->assertNotNull($page->id);
        
        $page->comments_enabled = true;
        $page->save();
        
        $page = new Page($page->id);
        $this->assertTrue($page->comments_enabled);
        
        $this->cleanUp();
    }

    public function testUpdatePageWithCommentsEnabledFalse()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->systemname = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->autor = $this->user->getId();
        $page->autor = 1;
        $page->group_id = 1;
        $page->save();
        
        $this->assertNotNull($page->id);
        
        $page->comments_enabled = false;
        $page->save();
        
        $page = new Page($page->id);
        $this->assertFalse($page->comments_enabled);
        
        $this->cleanUp();
    }

    public function testUpdatePageWithCommentsEnabledNull()
    {
        $page = new Page();
        $page->title = 'Unit Test ' . time();
        $page->systemname = 'unit-test-' . time();
        $page->language = 'de';
        $page->content = "Some Text";
        $page->comments_enabled = true;
        $page->autor = 1;
        $page->group_id = 1;
        $page->save();
        
        $this->assertNotNull($page->id);
        
        $page->comments_enabled = null;
        $page->save();
        
        $page = new Page($page->id);
        $this->assertNull($page->comments_enabled);
        
        $this->cleanUp();
    }

    public function testAreCommentsEnabledPageTrue()
    {
        $page = new Page();
        $page->comments_enabled = true;
        $this->assertTrue($page->areCommentsEnabled());
    }

    public function testAreCommentsEnabledPageFalse()
    {
        $page = new Page();
        $page->comments_enabled = false;
        $this->assertFalse($page->areCommentsEnabled());
    }

    public function testAreCommentsEnabledSettingsTrue()
    {
        $page = new Page();
        $page->comments_enabled = null;
        
        Settings::set("comments_enabled", "1");
        
        $this->assertTrue($page->areCommentsEnabled());
        $this->cleanUp();
    }

    public function testAreCommentsEnabledSettingsFalse()
    {
        $page = new Page();
        $page->comments_enabled = null;
        
        Settings::delete("comments_enabled");
        
        $this->assertFalse($page->areCommentsEnabled());
    }

    public function testHasCommentsReturnTrue()
    {
        throw new NotImplementedException();
    }

    public function testHasCommentsReturnFalse()
    {
        throw new NotImplementedException();
    }

    public function testGetCommentsReturnsArrayWithResults()
    {
        throw new NotImplementedException();
    }

    public function testGetCommentsReturnsEmptyArray()
    {
        throw new NotImplementedException();
    }
} 