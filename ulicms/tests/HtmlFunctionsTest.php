<?php
use UliCMS\HTML as html;
class HtmlFunctionsTest extends PHPUnit_Framework_TestCase {
	public function testText() {
		$this->assertEquals("line1<br />\nline2<br />\n&lt;strong&gt;line3&lt;/strong&gt;",html\text("line1\nline2\n<strong>line3</strong>"));
	}
	
}