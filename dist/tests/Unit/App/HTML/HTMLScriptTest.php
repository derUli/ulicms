<?php

use App\HTML\Script as Script;

class HTMLScriptTest extends \PHPUnit\Framework\TestCase {
    public function testInlineScript(): void {
        $this->assertEquals('<script>alert("Hello world!");</script>', Script::fromString('alert("Hello world!");'));
    }

    public function testInlineScriptAsync(): void {
        $this->assertEquals('<script async="async">alert("Hello world!");</script>', Script::fromString('alert("Hello world!");', true));
    }

    public function testInlineScriptDefer(): void {
        $this->assertEquals('<script defer="defer">alert("Hello world!");</script>', Script::fromString('alert("Hello world!");', false, true));
    }

    public function testInlineScriptAsyncAndDefer(): void {
        $this->assertEquals('<script async="async" defer="defer">alert("Hello world!");</script>', Script::fromString('alert("Hello world!");', true, true));
    }

    public function testInlineScriptAsyncAndFoo(): void {
        $this->assertEquals('<script async="async" foo1="hello" foo2="world">alert("Hello world!");</script>', Script::fromString('alert("Hello world!");', true, false, [
            'foo1' => 'hello',
            'foo2' => 'world'
        ]));
    }

    public function testExternalScript(): void {
        $this->assertEquals('<script src="folder/script.js"></script>', Script::fromFile('folder/script.js'));
    }

    public function testExternalScriptExisting(): void {
        $this->assertStringStartsWith('<script src="lib/js/global.js?time=', Script::fromFile('lib/js/global.js'));
    }

    public function testExternalScriptAsync(): void {
        $this->assertEquals('<script src="folder/script.js" async="async"></script>', Script::fromFile('folder/script.js', true));
    }

    public function testExternalScriptDefer(): void {
        $this->assertEquals('<script src="folder/script.js" defer="defer"></script>', Script::fromFile('folder/script.js', false, true));
    }

    public function testExternalScriptAsyncAndDefer(): void {
        $this->assertEquals('<script src="folder/script.js" async="async" defer="defer"></script>', Script::fromFile('folder/script.js', true, true));
    }

    public function testExternalScriptAsyncAndFoo(): void {
        $this->assertEquals('<script src="folder/script.js" async="async" foo1="hello" foo2="world"></script>', Script::fromFile('folder/script.js', true, false, [
            'foo1' => 'hello',
            'foo2' => 'world'
        ]));
    }
}
