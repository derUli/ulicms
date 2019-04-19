<?php
use UliCMS\HTML\Script as Script;

class HTMLScriptTest extends \PHPUnit\Framework\TestCase
{

    public function testInlineScript()
    {
        $this->assertEquals("<script type=\"text/javascript\">alert(\"Hello world!\");</script>", Script::FromString("alert(\"Hello world!\");"));
    }

    public function testInlineScriptAsync()
    {
        $this->assertEquals("<script type=\"text/javascript\" async=\"async\">alert(\"Hello world!\");</script>", Script::FromString("alert(\"Hello world!\");", true));
    }

    public function testInlineScriptDefer()
    {
        $this->assertEquals("<script type=\"text/javascript\" defer=\"defer\">alert(\"Hello world!\");</script>", Script::FromString("alert(\"Hello world!\");", false, true));
    }

    public function testInlineScriptAsyncAndDefer()
    {
        $this->assertEquals("<script type=\"text/javascript\" async=\"async\" defer=\"defer\">alert(\"Hello world!\");</script>", Script::FromString("alert(\"Hello world!\");", true, true));
    }

    public function testInlineScriptAsyncAndFoo()
    {
        $this->assertEquals("<script type=\"text/javascript\" async=\"async\" foo1=\"hello\" foo2=\"world\">alert(\"Hello world!\");</script>", Script::FromString("alert(\"Hello world!\");", true, false, array(
            "foo1" => "hello",
            "foo2" => "world"
        )));
    }

    public function testExternalScript()
    {
        $this->assertEquals("<script src=\"folder/script.js\" type=\"text/javascript\"></script>", Script::FromFile("folder/script.js"));
    }

    public function testExternalScriptAsync()
    {
        $this->assertEquals("<script src=\"folder/script.js\" type=\"text/javascript\" async=\"async\"></script>", Script::FromFile("folder/script.js", true));
    }

    public function testExternalScriptDefer()
    {
        $this->assertEquals("<script src=\"folder/script.js\" type=\"text/javascript\" defer=\"defer\"></script>", Script::FromFile("folder/script.js", false, true));
    }

    public function testExternalScriptAsyncAndDefer()
    {
        $this->assertEquals("<script src=\"folder/script.js\" type=\"text/javascript\" async=\"async\" defer=\"defer\"></script>", Script::FromFile("folder/script.js", true, true));
    }

    public function testExternalScriptAsyncAndFoo()
    {
        $this->assertEquals("<script src=\"folder/script.js\" type=\"text/javascript\" async=\"async\" foo1=\"hello\" foo2=\"world\"></script>", Script::FromFile("folder/script.js", true, false, array(
            "foo1" => "hello",
            "foo2" => "world"
        )));
    }
}