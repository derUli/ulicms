<?php

use App\Exceptions\SCSSCompileException;
use App\Utils\CacheUtil;

class MinifyTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        resetScriptQueue();
        resetStylesheetQueue();

        setSCSSImportPaths([]);
        Vars::delete('css_include_paths');
    }

    public function testScriptQueue()
    {
        $filemtime = 0;
        $files = [
            'node_modules/jquery/dist/jquery.js',
            'admin/scripts/global.js',
            'node_modules/bootbox/bootbox.js'
        ];
        foreach ($files as $file) {
            enqueueScriptFile($file);
            if (filemtime($file) > $filemtime) {
                $filemtime = filemtime($file);
            }
        }
        $this->assertCount(3, Vars::get('script_queue'));
        $this->assertEquals(
            'node_modules/jquery/dist/jquery.js',
            Vars::get('script_queue')[0]
        );
        $this->assertEquals(
            'node_modules/bootbox/bootbox.js',
            Vars::get('script_queue')[2]
        );

        resetScriptQueue();
        $this->assertCount(0, Vars::get('script_queue'));

        foreach ($files as $file) {
            enqueueScriptFile($file);
        }

        $html = getCombinedScriptHtml();
        $this->assertStringStartsWith(
            '<script src="content/cache/legacy/scripts/',
            $html
        );
        $this->assertStringContainsString('.js?time=', $html);
        $this->assertStringEndsWith('></script>', $html);

        $this->assertCount(0, Vars::get('script_queue'));
    }

    public function testCombinedScriptHTMLDeprecated()
    {
        $files = [
            'node_modules/jquery/dist/jquery.js',
            'admin/scripts/global.js',
            'node_modules/bootbox/bootbox.js'
        ];

        foreach ($files as $file) {
            enqueueScriptFile($file);
        }

        ob_start();
        combinedScriptHtml();
        $html = ob_get_clean();

        $this->assertStringStartsWith(
            '<script src="content/cache/legacy/scripts/',
            $html
        );
        $this->assertStringContainsString('.js?time=', $html);
        $this->assertStringEndsWith('></script>', $html);

        $this->assertCount(0, Vars::get('script_queue'));
    }

    public function testStylesheetQueue()
    {
        $filemtime = 0;
        $files = [
            'lib/css/core.scss',
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'node_modules/bootstrap/dist/css/bootstrap-theme.css',
            'admin/css/modern.scss'
        ];
        foreach ($files as $file) {
            enqueueStylesheet($file);
            if (filemtime($file) > $filemtime) {
                $filemtime = filemtime($file);
            }
        }
        $this->assertCount(4, Vars::get('stylesheet_queue'));
        $this->assertEquals(
            'node_modules/bootstrap/dist/css/bootstrap.css',
            Vars::get('stylesheet_queue')[1]
        );
        $this->assertEquals(
            'node_modules/bootstrap/dist/css/bootstrap-theme.css',
            Vars::get('stylesheet_queue')[2]
        );

        resetStylesheetQueue();
        $this->assertCount(0, Vars::get('stylesheet_queue'));

        foreach ($files as $file) {
            enqueueStylesheet($file);
        }

        $html = getCombinedStylesheetHTML();
        $this->assertStringStartsWith('<link rel="stylesheet" href="', $html);
        $this->assertStringContainsString('.css?time=', $html);
        $this->assertStringEndsWith('" type="text/css"/>', $html);

        $this->assertCount(0, Vars::get('script_queue'));
    }

    public function testCombinedStylesheetHtml()
    {
        $filemtime = 0;
        $files = [
            'lib/css/core.scss',
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'node_modules/bootstrap/dist/css/bootstrap-theme.css',
            'admin/css/modern.scss'
        ];

        foreach ($files as $file) {
            enqueueStylesheet($file);
        }

        ob_start();
        combinedStylesheetHtml();
        $html = ob_get_clean();
        $this->assertStringStartsWith('<link rel="stylesheet" href="', $html);
        $this->assertStringContainsString('.css?time=', $html);
        $this->assertStringEndsWith('" type="text/css"/>', $html);

        $this->assertCount(0, Vars::get('script_queue'));
    }

    public function testMinifySCSSExpectCSS()
    {
        unsetSCSSImportPaths();
        CacheUtil::getAdapter(true)->clear();
        $styles = [
            'tests/fixtures/scss/style1.scss',
            'tests/fixtures/scss/style2.scss',
            'lib/css/core.scss'
        ];
        foreach ($styles as $style) {
            enqueueStylesheet($style);
        }
        $expected = file_get_contents('tests/fixtures/scss/expected.css');

        $outputFile = minifyCSS();
        $real = file_get_contents($outputFile);
        $this->assertEquals($expected, $real);
    }

    public function testMinifySCSSThrowsException()
    {
        unsetSCSSImportPaths();
        CacheUtil::getAdapter(true)->clear();
        $style = 'tests/fixtures/scss/fail.scss';
        enqueueStylesheet($style);

        try {
            minifyCSS();
            $this->fail('Expected exception not thrown');
        } catch (SCSSCompileException $e) {
            $this->assertStringStartsWith(
                'Compilation of tests/fixtures/scss/fail.scss failed: parse error: failed at',
                $e->getMessage()
            );
            $this->assertStringEndsWith(
                '(stdin) on line 5, at column 5',
                $e->getMessage()
            );
        } finally {
            resetStylesheetQueue();
        }
    }

    public function testSetSCSSImportPathsToNull()
    {
        $paths = [
            'folder1/foo/bar',
            'folder2/another/folder'
        ];
        setSCSSImportPaths($paths);

        setSCSSImportPaths(null);
        $this->assertCount(1, getSCSSImportPaths());
        $this->assertEquals(
            str_replace(
                '\\',
                '/',
                ULICMS_ROOT
            ),
            getSCSSImportPaths()[0]
        );
    }

    public function testSetAndGetSCSSImportPaths()
    {
        $paths = [
            'folder1/foo/bar',
            'folder2/another/folder'
        ];
        $this->assertNull(getSCSSImportPaths());
        setSCSSImportPaths($paths);

        $this->assertEquals($paths, getSCSSImportPaths());
        unsetSCSSImportPaths();

        $this->assertNull(getSCSSImportPaths());
    }

    public function testCompileSCSS()
    {
        setSCSSImportPaths(
            [
                'folder1/foo/bar',
                'folder2/another/folder'
            ]
        );
        $code = compileSCSS(
            Path::resolve(
                'ULICMS_ROOT/lib/css/core.scss'
            )
        );
        $this->assertStringContainsString('.antispam_honeypot', $code);
        $this->assertStringContainsString('span.blog_article_next', $code);
    }

    public function testCompileSCSSToFile()
    {
        sureRemoveDir(
            Path::resolve(
                'ULICMS_CACHE/stylesheets'
            )
        );
        setSCSSImportPaths(
            [
                'folder1/foo/bar',
                'folder2/another/folder'
            ]
        );
        $filename = compileSCSSToFile(
            Path::resolve(
                'ULICMS_ROOT/lib/css/core.scss'
            )
        );

        $this->assertStringEndsWith('.css', $filename);

        $code = file_get_contents($filename);
        $this->assertStringContainsString('.antispam_honeypot', $code);
        $this->assertStringContainsString('span.blog_article_next', $code);
    }

    public function testGetAllCombinedHtml()
    {
        $this->enqeueStuff();
        $html = get_all_combined_html();

        $this->assertStringContainsString(
            '<script src="content/cache/legacy/scripts/',
            $html
        );
        $this->assertStringContainsString(
            '<link rel="stylesheet" href="content/cache/legacy/stylesheets/',
            $html
        );
    }

    public function testAllCombinedHtml()
    {
        $this->enqeueStuff();

        ob_start();
        all_combined_html();
        $html = ob_get_clean();

        $this->assertStringContainsString(
            '<script src="content/cache/legacy/scripts/',
            $html
        );
        $this->assertStringContainsString(
            '<link rel="stylesheet" href="content/cache/legacy/stylesheets/',
            $html
        );
    }

    private function enqeueStuff()
    {
        $files = [
            'node_modules/jquery/dist/jquery.js',
            'admin/scripts/global.js',
            'node_modules/bootbox/bootbox.js'
        ];
        foreach ($files as $file) {
            enqueueScriptFile($file);
        }

        $files = [
            'lib/css/core.scss',
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'node_modules/bootstrap/dist/css/bootstrap-theme.css',
            'admin/css/modern.scss'
        ];
        foreach ($files as $file) {
            enqueueStylesheet($file);
        }
    }
}
