<?php

class ModuleHelperTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        $_SESSION['language'] = 'en';
        require_once getLanguageFilePath('en');
        $_SERVER = [];
        $_SERVER['REQUEST_URI'] = '/other-url.html?param=value';
    }

    protected function tearDown(): void {
        chdir(ULICMS_ROOT);

        $_SERVER = [];

        Database::deleteFrom('content', "title like 'Unit Test%'");
    }

    public function testUnderscoreToCamel(): void {
        $this->assertEquals(
            'myModuleName',
            \App\Helpers\ModuleHelper::underscoreToCamel('my_module_name')
        );
        $this->assertEquals(
            'init',
            \App\Helpers\ModuleHelper::underscoreToCamel('init')
        );
        $this->assertEquals(
            'myModuleName',
            \App\Helpers\ModuleHelper::underscoreToCamel('My_Module_Name')
        );
    }

    public function testBuildModuleRessourcePath(): void {
        $this->assertEquals(
            'content/modules/my_module/js/coolscript.js',
            \App\Helpers\ModuleHelper::buildModuleRessourcePath(
                'my_module',
                'js/coolscript.js'
            )
        );
        $this->assertEquals(
            'content/modules/other_module/test.css',
            \App\Helpers\ModuleHelper::buildModuleRessourcePath(
                'other_module',
                'test.css'
            )
        );
    }

    public function testBuildAdminURL(): void {
        $this->assertEquals(
            '?action=module_settings&module=my_module&var1=hallo&var2=welt',
            \App\Helpers\ModuleHelper::buildAdminURL(
                'my_module',
                'var1=hallo&var2=welt'
            )
        );
        $this->assertEquals(
            '?action=module_settings&module=other_module',
            \App\Helpers\ModuleHelper::buildAdminURL('other_module')
        );
    }

    public function testGetFirstPageWithModule(): void {
        $_SESSION['language'] = 'de';
        $this->assertEquals(6, \App\Helpers\ModuleHelper::getFirstPageWithModule()->id);
        $this->assertEquals(6, \App\Helpers\ModuleHelper::getFirstPageWithModule('fortune2')->id);
        $this->assertEquals(6, \App\Helpers\ModuleHelper::getFirstPageWithModule('fortune2', 'de')->id);
        $this->assertEquals(13, \App\Helpers\ModuleHelper::getFirstPageWithModule('fortune2', 'en')->id);
        $this->assertEquals(6, \App\Helpers\ModuleHelper::getFirstPageWithModule(null, 'de')->id);
        $this->assertEquals(13, \App\Helpers\ModuleHelper::getFirstPageWithModule(null, 'en')->id);

        $_SESSION['language'] = 'en';
        $this->assertEquals(13, \App\Helpers\ModuleHelper::getFirstPageWithModule()->id);
        $this->assertEquals(13, \App\Helpers\ModuleHelper::getFirstPageWithModule('fortune2')->id);

        $this->assertNull(\App\Helpers\ModuleHelper::getFirstPageWithModule('gibts_nicht_modul'));
    }

    public function testIsEmbedModule(): void {
        $this->assertTrue(\App\Helpers\ModuleHelper::isEmbedModule('fortune'));
        $this->assertFalse(\App\Helpers\ModuleHelper::isEmbedModule('slicknav'));
    }

    public function testGetAllEmbedModule(): void {
        $embedModules = \App\Helpers\ModuleHelper::getAllEmbedModules();
        $this->assertTrue(in_array('fortune2', $embedModules));
        $this->assertFalse(in_array('slicknav', $embedModules));
    }

    public function testGetMainController(): void {
        $this->assertInstanceOf('Fortune', \App\Helpers\ModuleHelper::getMainController('fortune2'));
        $this->assertNull(\App\Helpers\ModuleHelper::getMainController('slicknav'));
        $this->assertNull(\App\Helpers\ModuleHelper::getMainController('not_a_module'));
    }

    public function testGetMainClass(): void {
        $this->assertInstanceOf('Fortune', \App\Helpers\ModuleHelper::getMainClass('fortune2'));
        $this->assertNull(\App\Helpers\ModuleHelper::getMainClass('slicknav'));
        $this->assertNull(\App\Helpers\ModuleHelper::getMainClass('not_a_module'));
    }

    public function testBuildMethodCall(): void {
        $this->assertEquals('sClass=MyClass&sMethod=MyMethod', \App\Helpers\ModuleHelper::buildMethodCall('MyClass', 'MyMethod'));
        $this->assertEquals('sClass=My_Class&sMethod=My_Method', \App\Helpers\ModuleHelper::buildMethodCall('My_Class', 'My_Method'));
        $this->assertEquals('sClass=My_Class&sMethod=My_Method&var1=hello&var2=world', \App\Helpers\ModuleHelper::buildMethodCall('My_Class', 'My_Method', 'var1=hello&var2=world'));
    }

    public function testBuildHTMLAttributesFromArray(): void {
        $this->assertEquals('class="myclass" id="myid" style="border:0"', \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray([
            'class' => 'myclass',
            'id' => 'myid',
            'style' => 'border:0'
        ]));
    }

    public function testBuildMethodCallFormWithHtmlAttributes(): void {
        $html = \App\Helpers\ModuleHelper::buildMethodCallForm('MyClass', 'MyMethod', [], 'post', [
            'class' => 'myclass',
            'onsubmit' => "return confirm('Do you really want to do that')"
        ]);
        $this->assertEquals('<form action="index.php" method="post" class="myclass" onsubmit="return confirm(&#039;Do you really want to do that&#039;)">' . get_csrf_token_html() . '<input type="hidden" name="sClass" value="MyClass">' . '<input type="hidden" name="sMethod" value="MyMethod">', $html);
    }

    public function testBuildMethodCallUploadFormWithHtmlAttributes(): void {
        $html = \App\Helpers\ModuleHelper::buildMethodCallUploadForm('MyClass', 'MyMethod', [], 'post', [
            'class' => 'myclass',
            'onsubmit' => "return confirm('Do you really want to do that')"
        ]);
        $this->assertEquals('<form action="index.php" method="post" class="myclass" onsubmit="return confirm(&#039;Do you really want to do that&#039;)" enctype="multipart/form-data">' . get_csrf_token_html() . '<input type="hidden" name="sClass" value="MyClass">' . '<input type="hidden" name="sMethod" value="MyMethod">', $html);
    }

    public function testDeleteButton(): void {
        $expected = optimizeHtml(
            '<form action="index.php?action=contacts" method="post" class="delete-form"><input type="hidden" name="csrf_token" value="' . get_csrf_token() . '"><input type="hidden" name="delete" value="123"><input type="image" src="admin/gfx/delete.png" alt="Delete" title="Delete"></form>'
        );
        $this->assertEquals($expected, \App\Helpers\ModuleHelper::deleteButton('index.php?action=contacts', [
            'delete' => '123'
        ]));
    }

    public function testBuildQueryString(): void {
        $data = [
            'foo' => 'bar',
            'baz' => 'boom',
            'kuh' => 'milch',
            'php' => 'hypertext processor'
        ];
        $this->assertEquals('foo=bar&baz=boom&kuh=milch&php=hypertext+processor', \App\Helpers\ModuleHelper::buildQueryString($data, false));
        $this->assertEquals('foo=bar&amp;baz=boom&amp;kuh=milch&amp;php=hypertext+processor', \App\Helpers\ModuleHelper::buildQueryString($data, true));
    }

    public function testBuildMethodCallButton(): void {
        $this->assertEquals('<form action="index.php" method="post">' . get_csrf_token_html() . '<input type="hidden" name="sClass" value="MyClass"><input type="hidden" name="sMethod" value="myMethod"><button class="btn btn-default" type="submit">Say Hello</button></form>', \App\Helpers\ModuleHelper::buildMethodCallButton('MyClass', 'myMethod', 'Say Hello'));
    }

    public function testEndForm(): void {
        $this->assertEquals('</form>', \App\Helpers\ModuleHelper::endForm());
    }

    public function testGetFullPageURLByID(): void {
        $_SESSION['language'] = 'de';
        $_SERVER['HTTP_HOST'] = 'company.com';
        $this->assertEquals('http://company.com/willkommen', \App\Helpers\ModuleHelper::getFullPageURLByID(1));

        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals('https://company.com/willkommen', \App\Helpers\ModuleHelper::getFullPageURLByID(1));

        unset($_SERVER['HTTP_HOST'], $_SERVER['HTTPS']);

    }

    public function testGetBaseUrl(): void {
        $_SERVER['HTTP_HOST'] = 'company.com';
        $_SERVER['REQUEST_URI'] = '/foo.png';
        $this->assertEquals('http://company.com/', \App\Helpers\ModuleHelper::getBaseUrl());
        $this->assertEquals('http://company.com/admin/gfx/logo.png', \App\Helpers\ModuleHelper::getBaseUrl('/admin/gfx/logo.png'));
        $_SERVER['REQUEST_URI'] = '/subdir/foo.png';
        $this->assertEquals('http://company.com/subdir/', \App\Helpers\ModuleHelper::getBaseUrl());
        $this->assertEquals('http://company.com/subdir/admin/gfx/logo.png', \App\Helpers\ModuleHelper::getBaseUrl('/admin/gfx/logo.png'));
    }

    public function testGetBaseUrlInAdminDir(): void {
        chdir('admin/');
        $_SERVER['HTTP_HOST'] = 'company.com';
        $_SERVER['REQUEST_URI'] = '/foo.png';
        $this->assertEquals('http://company.com/', \App\Helpers\ModuleHelper::getBaseUrl());
        $this->assertEquals(
            'http://company.com/admin/gfx/logo.png',
            \App\Helpers\ModuleHelper::getBaseUrl('/admin/gfx/logo.png')
        );
        $_SERVER['REQUEST_URI'] = '/subdir/foo.png';
        $this->assertEquals(
            'http://company.com/subdir/',
            \App\Helpers\ModuleHelper::getBaseUrl()
        );
        $this->assertEquals(
            'http://company.com/subdir/admin/gfx/logo.png',
            \App\Helpers\ModuleHelper::getBaseUrl('/admin/gfx/logo.png')
        );
        chdir(ULICMS_ROOT);
    }

    public function testBuildActionUrl(): void {
        $this->assertEquals(
            'admin/?action=foobar&hello=world',
            \App\Helpers\ModuleHelper::buildActionURL('foobar', 'hello=world', true)
        );
    }

    private function getPageWithShortcode(): Page {
        $page = new Page();
        $page->title = 'Unit Test ' . uniqid();
        $page->slug = 'unit-test-' . uniqid();
        $page->menu = 'none';
        $page->language = 'de';
        $page->article_date = 1413821696;
        $page->author_id = 1;
        $page->group_id = 1;
        $page->content = '[module=foo]';
        $page->save();

        return $page;
    }
}
