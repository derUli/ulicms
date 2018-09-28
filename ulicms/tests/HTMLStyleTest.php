<?php
use UliCMS\HTML\Style as Style;
class HTMLStyleTest extends \PHPUnit\Framework\TestCase {
	public function testInlineCSS() {
		$this->assertEquals ( "<style type=\"text/css\">body{background-color:red;}</style>", Style::FromString ( "body{background-color:red;}" ) );
	}
	public function testInlineCSSWithMedia() {
		$this->assertEquals ( "<style type=\"text/css\" media=\"handheld\">body{background-color:red;}</style>", Style::FromString ( "body{background-color:red;}", "handheld" ) );
	}
	public function testInlineCSSWithMediaAndTwoFoos() {
		$this->assertEquals ( "<style type=\"text/css\" media=\"handheld\" foo1=\"hello\" foo2=\"world\">body{background-color:red;}</style>", Style::FromString ( "body{background-color:red;}", "handheld", array (
				"foo1" => "hello",
				"foo2" => "world" 
		) ) );
	}
	public function testExternalCSS() {
		$this->assertEquals ( "<link rel=\"stylesheet\" href=\"admin/css/modern.css\" type=\"text/css\"/>", Style::FromExternalFile ( "admin/css/modern.css" ) );
	}
	public function testExternalCSSWithMedia() {
		$this->assertEquals ( "<link rel=\"stylesheet\" href=\"admin/css/modern.css\" type=\"text/css\" media=\"all\"/>", Style::FromExternalFile ( "admin/css/modern.css", "all" ) );
	}
	public function testExternalCSSWithMediaAndTwoFoos() {
		$this->assertEquals ( "<link rel=\"stylesheet\" href=\"admin/css/modern.css\" type=\"text/css\" media=\"all\" foo1=\"hello\" foo2=\"world\"/>", Style::FromExternalFile ( "admin/css/modern.css", "all", array (
				"foo1" => "hello",
				"foo2" => "world" 
		) ) );
	}
}