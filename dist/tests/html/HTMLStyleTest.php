<?php

use App\HTML\Style as Style;
use App\Utils\File;

class HTMLStyleTest extends \PHPUnit\Framework\TestCase
{
    public function testInlineCSS()
    {
        $this->assertEquals('<style>body{background-color:red;}</style>', Style::fromString('body{background-color:red;}'));
    }

    public function testInlineCSSWithMedia()
    {
        $this->assertEquals('<style media="handheld">body{background-color:red;}</style>', Style::fromString('body{background-color:red;}', 'handheld'));
    }

    public function testInlineCSSWithMediaAndTwoFoos()
    {
        $this->assertEquals('<style media="handheld" foo1="hello" foo2="world">body{background-color:red;}</style>', Style::fromString('body{background-color:red;}', 'handheld', [
            'foo1' => 'hello',
            'foo2' => 'world'
        ]));
    }

    public function testExternalCSS()
    {
        $file = 'admin/css/modern.scss';
        $time = File::getLastChanged($file);
        $expected = "<link rel=\"stylesheet\" href=\"{$file}?time={$time}\" type=\"text/css\"/>";
        $this->assertEquals($expected, Style::fromExternalFile($file));
    }

    public function testExternalCSSWithMedia()
    {
        $file = 'admin/css/modern.scss';
        $time = File::getLastChanged($file);
        $expected = "<link rel=\"stylesheet\" href=\"{$file}?time={$time}\" type=\"text/css\" media=\"all\"/>";
        $this->assertEquals($expected, Style::fromExternalFile('admin/css/modern.scss', 'all'));
    }

    public function testExternalCSSWithMediaAndTwoFoos()
    {
        $file = 'admin/css/modern.scss';
        $time = File::getLastChanged($file);
        $expected = "<link rel=\"stylesheet\" href=\"{$file}?time={$time}\" type=\"text/css\" media=\"all\" foo1=\"hello\" foo2=\"world\"/>";
        $this->assertEquals($expected, Style::fromExternalFile('admin/css/modern.scss', 'all', [
            'foo1' => 'hello',
            'foo2' => 'world'
        ]));
    }
}
