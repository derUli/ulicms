<?php

include_once ULICMS_ROOT . "/api.php";

use UliCMS\Exceptions\SCSSCompileException;

class MinifyTest extends \PHPUnit\Framework\TestCase {

    public function testScriptQueue() {
        $filemtime = 0;
        $files = array(
            "node_modules/jquery/dist/jquery.js",
            "admin/scripts/global.js",
            "admin/scripts/url.min.js"
        );
        foreach ($files as $file) {
            enqueueScriptFile($file);
            if (filemtime($file) > $filemtime) {
                $filemtime = filemtime($file);
            }
        }
        $this->assertCount(3, $_SERVER["script_queue"]);
        $this->assertEquals("node_modules/jquery/dist/jquery.js", $_SERVER["script_queue"][0]);
        $this->assertEquals("admin/scripts/url.min.js", $_SERVER["script_queue"][2]);

        resetScriptQueue();
        $this->assertCount(0, $_SERVER["script_queue"]);

        foreach ($files as $file) {
            enqueueScriptFile($file);
        }

        $this->assertEquals('<script src="?output_scripts=node_modules/jquery/dist/jquery.js;admin/scripts/global.js;admin/scripts/url.min.js&amp;time=' . $filemtime . '" type="text/javascript"></script>', getCombinedScriptHtml());
        $this->assertCount(0, $_SERVER["script_queue"]);
    }

    public function testStylesheetQueue() {
        $filemtime = 0;
        $files = array(
            "core.css",
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
        $this->assertCount(4, $_SERVER["stylesheet_queue"]);
        $this->assertEquals("node_modules/bootstrap/dist/css/bootstrap.css", $_SERVER["stylesheet_queue"][1]);
        $this->assertEquals("node_modules/bootstrap/dist/css/bootstrap-theme.css", $_SERVER["stylesheet_queue"][2]);

        resetStylesheetQueue();
        $this->assertCount(0, $_SERVER["stylesheet_queue"]);

        foreach ($files as $file) {
            enqueueStylesheet($file);
        }

        $this->assertEquals('<link rel="stylesheet" href="?output_stylesheets=core.css;node_modules/bootstrap/dist/css/bootstrap.css;node_modules/bootstrap/dist/css/bootstrap-theme.css;admin/css/modern.scss&amp;time=' . $filemtime . '" type="text/css"/>', getCombinedStylesheetHtml());
        $this->assertCount(0, $_SERVER["stylesheet_queue"]);
    }

    public function testMinifySCSSExpectCSS() {
        unsetSCSSImportPaths();
        CacheUtil::getAdapter(true)->clear();
        $style = array(
            "tests/fixtures/scss/style1.scss",
            "tests/fixtures/scss/style2.scss",
            "core.css"
        );
        $_GET["output_stylesheets"] = implode(";", $style);
        $_GET["time"] = time();
        $_SERVER["REQUEST_URI"] = getCombinedStylesheetURL();
        $expected = file_get_contents("tests/fixtures/scss/expected.css");

        $real = getCombinedStylesheets(true);
        $this->assertEquals($real, $expected);
    }

    public function testMinifySCSSThrowsException() {
        unsetSCSSImportPaths();
        CacheUtil::getAdapter(true)->clear();
        $style = array(
            "tests/fixtures/scss/fail.scss"
        );
        $_GET["output_stylesheets"] = implode(";", $style);
        $_GET["time"] = time();
        $_SERVER["REQUEST_URI"] = getCombinedStylesheetURL();
        try {
            getCombinedStylesheets(true);
            $this->fail("Expected exception not thrown");
        } catch (SCSSCompileException $e) {
            $this->assertEquals("Compilation of tests/fixtures/scss/fail.scss failed: parse error: failed at `wid012321:56z754654$$` (stdin) on line 5", $e->getMessage());
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
