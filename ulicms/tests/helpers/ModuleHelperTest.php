<?php

class ModuleHelperTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $_SESSION["language"] = "en";
        require_once getLanguageFilePath("en");
        $_SERVER = [];
        $_SERVER["REQUEST_URI"] = "/other-url.html?param=value";
    }

    protected function tearDown(): void
    {
        chdir(ULICMS_ROOT);
        
        $_SERVER = [];

        Database::deleteFrom("content", "title like 'Unit Test%'");
    }

    private function getPageWithShortcode(): Page
    {
        $page = new Page();
        $page->title = "Unit Test " . uniqid();
        $page->slug = "unit-test-" . uniqid();
        $page->menu = "none";
        $page->language = "tlh";
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->content = "[module=fortune2]";
        $page->save();
        
        return $page;
    }

    public function testUnderscoreToCamel()
    {
        $this->assertEquals(
            "myModuleName",
            ModuleHelper::underscoreToCamel("my_module_name")
        );
        $this->assertEquals(
            "init",
            ModuleHelper::underscoreToCamel("init")
        );
        $this->assertEquals(
            "myModuleName",
            ModuleHelper::underscoreToCamel("My_Module_Name")
        );
    }

    public function testBuildModuleRessourcePath()
    {
        $this->assertEquals(
            "content/modules/my_module/js/coolscript.js",
            ModuleHelper::buildModuleRessourcePath(
                "my_module",
                "js/coolscript.js"
            )
        );
        $this->assertEquals(
            "content/modules/other_module/test.css",
            ModuleHelper::buildModuleRessourcePath(
                "other_module",
                "test.css"
            )
        );
    }

    public function testBuildAdminURL()
    {
        $this->assertEquals(
            "?action=module_settings&module=my_module&var1=hallo&var2=welt",
            ModuleHelper::buildAdminURL(
                "my_module",
                "var1=hallo&var2=welt"
            )
        );
        $this->assertEquals(
            "?action=module_settings&module=other_module",
            ModuleHelper::buildAdminURL("other_module")
        );
    }

    public function testGetFirstPageWithModule()
    {
        $_SESSION["language"] = "de";
        $this->assertEquals(6, ModuleHelper::getFirstPageWithModule()->id);
        $this->assertEquals(7, ModuleHelper::getFirstPageWithModule("pfbc_sample")->id);
        $this->assertEquals(6, ModuleHelper::getFirstPageWithModule("fortune2")->id);
        $this->assertEquals(7, ModuleHelper::getFirstPageWithModule("pfbc_sample", "de")->id);
        $this->assertEquals(6, ModuleHelper::getFirstPageWithModule("fortune2", "de")->id);
        $this->assertEquals(14, ModuleHelper::getFirstPageWithModule("pfbc_sample", "en")->id);
        $this->assertEquals(13, ModuleHelper::getFirstPageWithModule("fortune2", "en")->id);
        $this->assertEquals(6, ModuleHelper::getFirstPageWithModule(null, "de")->id);
        $this->assertEquals(13, ModuleHelper::getFirstPageWithModule(null, "en")->id);

        $_SESSION["language"] = "en";
        $this->assertEquals(13, ModuleHelper::getFirstPageWithModule()->id);
        $this->assertEquals(14, ModuleHelper::getFirstPageWithModule("pfbc_sample")->id);
        $this->assertEquals(13, ModuleHelper::getFirstPageWithModule("fortune2")->id);

        $this->assertNull(ModuleHelper::getFirstPageWithModule('gibts_nicht_modul'));

        $expectedPage = $this->getPageWithShortcode();
        $actualPage = ModuleHelper::getFirstPageWithModule(null, 'tlh');
        $this->assertNotNull($actualPage);
        $this->assertEquals($expectedPage->getId(), $actualPage->id);
    }

    public function testIsEmbedModule()
    {
        $this->assertTrue(ModuleHelper::isEmbedModule("fortune"));
        $this->assertFalse(ModuleHelper::isEmbedModule("slicknav"));
    }

    public function testGetAllEmbedModule()
    {
        $embedModules = ModuleHelper::getAllEmbedModules();
        $this->assertTrue(faster_in_array("fortune2", $embedModules));
        $this->assertFalse(faster_in_array("slicknav", $embedModules));
    }

    public function testGetMainController()
    {
        $this->assertInstanceOf("Fortune", ModuleHelper::getMainController("fortune2"));
        $this->assertNull(ModuleHelper::getMainController("slicknav"));
        $this->assertNull(ModuleHelper::getMainController("not_a_module"));
    }

    public function testGetMainClass()
    {
        $this->assertInstanceOf("Fortune", ModuleHelper::getMainClass("fortune2"));
        $this->assertNull(ModuleHelper::getMainClass("slicknav"));
        $this->assertNull(ModuleHelper::getMainClass("not_a_module"));
    }

    public function testBuildMethodCall()
    {
        $this->assertEquals("sClass=MyClass&sMethod=MyMethod", ModuleHelper::buildMethodCall("MyClass", "MyMethod"));
        $this->assertEquals("sClass=My_Class&sMethod=My_Method", ModuleHelper::buildMethodCall("My_Class", "My_Method"));
        $this->assertEquals("sClass=My_Class&sMethod=My_Method&var1=hello&var2=world", ModuleHelper::buildMethodCall("My_Class", "My_Method", "var1=hello&var2=world"));
    }

    public function testBuildHTMLAttributesFromArray()
    {
        $this->assertEquals('class="myclass" id="myid" style="border:0"', ModuleHelper::buildHTMLAttributesFromArray(array(
                    "class" => "myclass",
                    "id" => "myid",
                    "style" => "border:0"
        )));
    }

    public function testBuildMethodCallFormWithHtmlAttributes()
    {
        $html = ModuleHelper::buildMethodCallForm("MyClass", "MyMethod", [], "post", array(
                    "class" => "myclass",
                    "onsubmit" => "return confirm('Do you really want to do that')"
        ));
        $this->assertEquals('<form action="index.php" method="post" class="myclass" onsubmit="return confirm(&#039;Do you really want to do that&#039;)">' . get_csrf_token_html() . '<input type="hidden" name="sClass" value="MyClass">' . '<input type="hidden" name="sMethod" value="MyMethod">', $html);
    }

    public function testBuildMethodCallUploadFormWithHtmlAttributes()
    {
        $html = ModuleHelper::buildMethodCallUploadForm("MyClass", "MyMethod", [], "post", array(
                    "class" => "myclass",
                    "onsubmit" => "return confirm('Do you really want to do that')"
        ));
        $this->assertEquals('<form action="index.php" method="post" class="myclass" onsubmit="return confirm(&#039;Do you really want to do that&#039;)" enctype="multipart/form-data">' . get_csrf_token_html() . '<input type="hidden" name="sClass" value="MyClass">' . '<input type="hidden" name="sMethod" value="MyMethod">', $html);
    }

    public function testDeleteButton()
    {
        $expected = optimizeHtml(
            '<form action="index.php?action=contacts" method="post" class="delete-form"><input type="hidden" name="csrf_token" value="' . get_csrf_token() . '"><input type="hidden" name="delete" value="123"><input type="image" src="admin/gfx/delete.png" alt="Delete" title="Delete"></form>'
        );
        $this->assertEquals($expected, ModuleHelper::deleteButton("index.php?action=contacts", array(
                    "delete" => "123"
        )));
    }

    public function testBuildQueryString()
    {
        $data = array(
            'foo' => 'bar',
            'baz' => 'boom',
            'kuh' => 'milch',
            'php' => 'hypertext processor'
        );
        $this->assertEquals("foo=bar&baz=boom&kuh=milch&php=hypertext+processor", ModuleHelper::buildQueryString($data, false));
        $this->assertEquals("foo=bar&amp;baz=boom&amp;kuh=milch&amp;php=hypertext+processor", ModuleHelper::buildQueryString($data, true));
    }

    public function testBuildMethodCallButton()
    {
        $this->assertEquals('<form action="index.php" method="post">' . get_csrf_token_html() . '<input type="hidden" name="sClass" value="MyClass"><input type="hidden" name="sMethod" value="myMethod"><button class="btn btn-default" type="submit">Say Hello</button></form>', ModuleHelper::buildMethodCallButton("MyClass", "myMethod", "Say Hello"));
    }

    public function testEndForm()
    {
        $this->assertEquals("</form>", ModuleHelper::endForm());
    }

    public function testGetFullPageURLByID()
    {
        $_SESSION["language"] = "de";
        $_SERVER['HTTP_HOST'] = "company.com";
        $this->assertEquals("http://company.com/willkommen.html", ModuleHelper::getFullPageURLByID(1));

        $_SERVER['HTTPS'] = "on";
        $this->assertEquals("https://company.com/willkommen.html", ModuleHelper::getFullPageURLByID(1));

        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['HTTPS']);
    }

    public function testGetBaseUrl()
    {
        $_SERVER['HTTP_HOST'] = "company.com";
        $_SERVER["REQUEST_URI"] = "/foo.png";
        $this->assertEquals("http://company.com/", ModuleHelper::getBaseUrl());
        $this->assertEquals("http://company.com/admin/gfx/logo.png", ModuleHelper::getBaseUrl("/admin/gfx/logo.png"));
        $_SERVER["REQUEST_URI"] = "/subdir/foo.png";
        $this->assertEquals("http://company.com/subdir/", ModuleHelper::getBaseUrl());
        $this->assertEquals("http://company.com/subdir/admin/gfx/logo.png", ModuleHelper::getBaseUrl("/admin/gfx/logo.png"));
    }

    public function testGetBaseUrlInAdminDir()
    {
        chdir("admin/");
        $_SERVER['HTTP_HOST'] = "company.com";
        $_SERVER["REQUEST_URI"] = "/foo.png";
        $this->assertEquals("http://company.com/", ModuleHelper::getBaseUrl());
        $this->assertEquals(
            "http://company.com/admin/gfx/logo.png",
            ModuleHelper::getBaseUrl("/admin/gfx/logo.png")
        );
        $_SERVER["REQUEST_URI"] = "/subdir/foo.png";
        $this->assertEquals(
            "http://company.com/subdir/",
            ModuleHelper::getBaseUrl()
        );
        $this->assertEquals(
            "http://company.com/subdir/admin/gfx/logo.png",
            ModuleHelper::getBaseUrl("/admin/gfx/logo.png")
        );
        chdir(ULICMS_ROOT);
    }

    public function testBuildActionUrl()
    {
        $this->assertEquals(
            "admin/?action=foobar&hello=world",
            ModuleHelper::buildActionURL("foobar", "hello=world", true)
        );
    }
}
