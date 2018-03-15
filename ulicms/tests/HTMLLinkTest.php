<?php
use UliCMS\HTML\Link as Link;
class HTMLLinkTest extends PHPUnit_Framework_TestCase {
	public function testLink() {
		$this->assertEquals ( "<a href=\"https://www.google.com\">Google</a>", Link::Link ( "https://www.google.com", "Google" ) );
	}
	public function testLinkWithAdditionalAttribute() {
		$this->assertEquals ( "<a href=\"https://www.google.com\" target=\"_blank\">Google</a>", Link::Link ( "https://www.google.com", "Google", array (
				"target" => "_blank" 
		) ) );
	}
	public function testActionLink() {
		$this->assertEquals ( "<a href=\"?action=pages\">Pages</a>", Link::ActionLink ( "pages", "Pages" ) );
	}
	public function testActionLinkWithSuffix() {
		$this->assertEquals ( "<a href=\"?action=pages&amp;hello=world\">Pages</a>", Link::ActionLink ( "pages", "Pages", "hello=world" ) );
	}
	public function testLinkWithAdditionalAttributes() {
		$this->assertEquals ( "<a href=\"?action=pages\" target=\"_blank\" class=\"btn btn-primary\">Pages</a>", Link::ActionLink ( "pages", "Pages", null, array (
				"target" => "_blank",
				"class" => "btn btn-primary" 
		) ) );
	}
}