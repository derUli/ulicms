<?php

class PageTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        @session_start();
        $this->cleanUp();
    }

    public function tearDown()
    {
        @session_destroy();
        $this->cleanUp();
    }
    private function cleanUp(){
        Database::query("delete from {prefix}content where systemname = 'testdisableshortcodes'", true);
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
        $page->save();

        $_SESSION["language"] = 'de';
        $_GET["seite"] = "testdisableshortcodes";

        $this->assertTrue(str_contains(get_csrf_token_html(), get_content()));
        $this->assertFalse(str_contains("[csrf_token_html]", get_content()));
        $this->cleanUp();

        unset($_SESSION["language"]);
        unset($_GET["seite"]);
    }
} 