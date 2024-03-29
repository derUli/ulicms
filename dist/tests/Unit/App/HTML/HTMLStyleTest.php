<?php

use App\HTML\Style as Style;
use App\Utils\File;

class HTMLStyleTest extends \PHPUnit\Framework\TestCase {
    public function testInlineCSS(): void {
        $this->assertEquals('<style>body{background-color:red;}</style>', Style::fromString('body{background-color:red;}'));
    }

    public function testInlineCSSWithMedia(): void {
        $this->assertEquals('<style media="handheld">body{background-color:red;}</style>', Style::fromString('body{background-color:red;}', 'handheld'));
    }

    public function testInlineCSSWithMediaAndTwoFoos(): void {
        $this->assertEquals('<style media="handheld" foo1="hello" foo2="world">body{background-color:red;}</style>', Style::fromString('body{background-color:red;}', 'handheld', [
            'foo1' => 'hello',
            'foo2' => 'world'
        ]));
    }

    public function testExternalCSS(): void {
        $file = 'admin/css/main.scss';
        $time = File::getLastChanged($file);
        $expected = "<link rel=\"stylesheet\" href=\"{$file}?time={$time}\" type=\"text/css\"/>";
        $this->assertEquals($expected, Style::fromExternalFile($file));
    }

    public function testExternalCSSWithMedia(): void {
        $file = 'admin/css/main.scss';
        $time = File::getLastChanged($file);
        $expected = "<link rel=\"stylesheet\" href=\"{$file}?time={$time}\" type=\"text/css\" media=\"all\"/>";
        $this->assertEquals($expected, Style::fromExternalFile('admin/css/main.scss', 'all'));
    }

    public function testExternalCSSWithMediaAndTwoFoos(): void {
        $file = 'admin/css/main.scss';
        $time = File::getLastChanged($file);
        $expected = "<link rel=\"stylesheet\" href=\"{$file}?time={$time}\" type=\"text/css\" media=\"all\" foo1=\"hello\" foo2=\"world\"/>";
        $this->assertEquals($expected, Style::fromExternalFile('admin/css/main.scss', 'all', [
            'foo1' => 'hello',
            'foo2' => 'world'
        ]));
    }
}
