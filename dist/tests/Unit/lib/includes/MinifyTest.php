<?php

use App\Exceptions\SCSSCompileException;
use App\Utils\CacheUtil;

class MinifyTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        resetScriptQueue();
        resetStylesheetQueue();

        setSCSSImportPaths([]);
        \App\Storages\Vars::delete('css_include_paths');
    }

    public function testScriptQueue(): void {
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
        $this->assertCount(3, \App\Storages\Vars::get('script_queue'));
        $this->assertEquals(
            'node_modules/jquery/dist/jquery.js',
            \App\Storages\Vars::get('script_queue')[0]
        );
        $this->assertEquals(
            'node_modules/bootbox/bootbox.js',
            \App\Storages\Vars::get('script_queue')[2]
        );

        resetScriptQueue();
        $this->assertCount(0, \App\Storages\Vars::get('script_queue'));

        foreach ($files as $file) {
            enqueueScriptFile($file);
        }

        $html = getCombinedScriptHtml();
        $this->assertStringStartsWith(
            '<script src="content/generated/public/scripts/',
            $html
        );
        $this->assertStringContainsString('.js?time=', $html);
        $this->assertStringEndsWith('></script>', $html);

        $this->assertCount(0, \App\Storages\Vars::get('script_queue'));
    }

    public function testCombinedScriptHTMLDeprecated(): void {
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
            '<script src="content/generated/public/scripts/',
            $html
        );
        $this->assertStringContainsString('.js?time=', $html);
        $this->assertStringEndsWith('></script>', $html);

        $this->assertCount(0, \App\Storages\Vars::get('script_queue'));
    }

    public function testStylesheetQueue(): void {
        $filemtime = 0;
        $files = [
            'lib/css/core.scss',
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'admin/css/main.scss'
        ];

        foreach ($files as $file) {
            enqueueStylesheet($file);
            if (filemtime($file) > $filemtime) {
                $filemtime = filemtime($file);
            }
        }

        $this->assertCount(3, \App\Storages\Vars::get('stylesheet_queue'));
        $this->assertEquals(
            'node_modules/bootstrap/dist/css/bootstrap.css',
            \App\Storages\Vars::get('stylesheet_queue')[1]
        );

        resetStylesheetQueue();
        $this->assertCount(0, \App\Storages\Vars::get('stylesheet_queue'));

        foreach ($files as $file) {
            enqueueStylesheet($file);
        }

        $html = getCombinedStylesheetHTML();
        $this->assertStringStartsWith('<link rel="stylesheet" href="', $html);
        $this->assertStringContainsString('.css?time=', $html);
        $this->assertStringEndsWith('" type="text/css"/>', $html);

        $this->assertCount(0, \App\Storages\Vars::get('script_queue'));
    }

    public function testCombinedStylesheetHtml(): void {
        $filemtime = 0;
        $files = [
            'lib/css/core.scss',
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'admin/css/main.scss'
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

        $this->assertCount(0, \App\Storages\Vars::get('script_queue'));
    }

    public function testMinifySCSSExpectCSS(): void {
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

    public function testMinifySCSSThrowsException(): void {
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

    public function testSetSCSSImportPathsToNull(): void {
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

    public function testSetAndGetSCSSImportPaths(): void {
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

    public function testCompileSCSS(): void {
        setSCSSImportPaths(
            [
                'folder1/foo/bar',
                'folder2/another/folder'
            ]
        );
        $code = compileSCSS(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/lib/css/core.scss'
            )
        );
        $this->assertStringContainsString('.antispam_honeypot', $code);
        $this->assertStringContainsString('span.blog_article_next', $code);
    }

    public function testCompileSCSSToFile(): void {
        sureRemoveDir(
            \App\Utils\Path::resolve(
                'ULICMS_GENERATED_PUBLIC/stylesheets'
            )
        );
        setSCSSImportPaths(
            [
                'folder1/foo/bar',
                'folder2/another/folder'
            ]
        );
        $filename = compileSCSSToFile(
            \App\Utils\Path::resolve(
                'ULICMS_ROOT/lib/css/core.scss'
            )
        );

        $this->assertStringEndsWith('.css', $filename);

        $code = file_get_contents($filename);
        $this->assertStringContainsString('.antispam_honeypot', $code);
        $this->assertStringContainsString('span.blog_article_next', $code);
    }

    public function testGetAllCombinedHtml(): void {
        $this->enqeueStuff();
        $html = get_all_combined_html();

        $this->assertStringContainsString(
            '<script src="content/generated/public/scripts/',
            $html
        );
        $this->assertStringContainsString(
            '<link rel="stylesheet" href="content/generated/public/stylesheets/',
            $html
        );
    }

    public function testAllCombinedHtml(): void {
        $this->enqeueStuff();

        ob_start();
        all_combined_html();
        $html = ob_get_clean();

        $this->assertStringContainsString(
            '<script src="content/generated/public/scripts/',
            $html
        );
        $this->assertStringContainsString(
            '<link rel="stylesheet" href="content/generated/public/stylesheets/',
            $html
        );
    }

    private function enqeueStuff(): void {
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
            'admin/css/main.scss'
        ];
        foreach ($files as $file) {
            enqueueStylesheet($file);
        }
    }
}
