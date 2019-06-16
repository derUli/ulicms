<?php

use UliCMS\Exceptions\SCSSCompileException;

class MinifyTest extends \PHPUnit\Framework\TestCase {

    public function testScriptQueue() {
        $filemtime = 0;
        $files = array(
            "node_modules/jquery/dist/jquery.js",
            "admin/scripts/global.js",
            "node_modules/js-url/url.min.js"
        );
        foreach ($files as $file) {
            enqueueScriptFile($file);
            if (filemtime($file) > $filemtime) {
                $filemtime = filemtime($file);
            }
        }
        $this->assertCount(3, Vars::get("script_queue"));
        $this->assertEquals("node_modules/jquery/dist/jquery.js", Vars::get("script_queue")[0]);
        $this->assertEquals("node_modules/js-url/url.min.js", Vars::get("script_queue")[2]);

        resetScriptQueue();
        $this->assertCount(0, Vars::get("script_queue"));

        foreach ($files as $file) {
            enqueueScriptFile($file);
        }

        $html = getCombinedScriptHtml();
        $this->assertStringStartsWith('<script src="content/cache/scripts/', $html);
        $this->assertContains(".js?time=", $html);
        $this->assertStringEndsWith('type="text/javascript"></script>', $html);

        $this->assertCount(0, Vars::get("script_queue"));
    }

    public function testStylesheetQueue() {
        $filemtime = 0;
        $files = array(
            "lib/css/core.css",
            "node_modules/bootstrap/dist/css/bootstrap.css",
            "node_modules/bootstrap/dist/css/bootstrap-theme.css",
            "admin/css/modern.scss"
        );
        foreach ($files as $file) {
            enqueueStylesheet($file);
            if (filemtime($file) > $filemtime) {
                $filemtime = filemtime($file);
            }
        }
        $this->assertCount(4, Vars::get("stylesheet_queue"));
        $this->assertEquals("node_modules/bootstrap/dist/css/bootstrap.css", Vars::get("stylesheet_queue")[1]);
        $this->assertEquals("node_modules/bootstrap/dist/css/bootstrap-theme.css", Vars::get("stylesheet_queue")[2]);

        resetStylesheetQueue();
        $this->assertCount(0, Vars::get("stylesheet_queue"));

        foreach ($files as $file) {
            enqueueStylesheet($file);
        }

        $html = getCombinedStylesheetHTML();
        $this->assertStringStartsWith('<link rel="stylesheet" href="', $html);
        $this->assertContains(".css?time=", $html);
        $this->assertStringEndsWith('" type="text/css"/>', $html);

        $this->assertCount(0, Vars::get("script_queue"));
    }

    public function testMinifySCSSExpectCSS() {
        unsetSCSSImportPaths();
        CacheUtil::getAdapter(true)->clear();
        $styles = array(
            "tests/fixtures/scss/style1.scss",
            "tests/fixtures/scss/style2.scss",
            "lib/css/core.css"
        );
        foreach ($styles as $style) {
            enqueueStylesheet($style);
        }
        $expected = file_get_contents("tests/fixtures/scss/expected.css");

        $outputFile = minifyCSS();
        $real = file_get_contents($outputFile);
        $this->assertEquals($expected, $real);
    }

    public function testMinifySCSSThrowsException() {
        unsetSCSSImportPaths();
        CacheUtil::getAdapter(true)->clear();
        $style = "tests/fixtures/scss/fail.scss";
        enqueueStylesheet($style);

        try {
            minifyCSS();
            $this->fail("Expected exception not thrown");
        } catch (SCSSCompileException $e) {
            $this->assertStringStartsWith("Compilation of tests/fixtures/scss/fail.scss failed: parse error: failed at", $e->getMessage());
            $this->assertStringEndsWith("(stdin) on line 5", $e->getMessage());
        } finally {
            resetStylesheetQueue();
        }
    }

    public function testSetAndGetSCSSImportPaths() {
        $paths = array(
            "folder1/foo/bar",
            "folder2/another/folder"
        );
        $this->assertNull(getSCSSImportPaths());
        setSCSSImportPaths($paths);

        $this->assertEquals($paths, getSCSSImportPaths());
        unsetSCSSImportPaths();

        $this->assertNull(getSCSSImportPaths());
    }

}
