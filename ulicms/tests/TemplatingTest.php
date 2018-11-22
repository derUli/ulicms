<?php
class TemplatingTest extends \PHPUnit\Framework\TestCase {
	public function tearDown() {
		$this->cleanUp ();
	}
	private function cleanUp() {
		unset ( $_GET ["seite"] );
	}
	public function testGetRequestedPageNameWithSystemNameSet() {
		$_GET ["seite"] = "foobar";
		$this->assertEquals ( "foobar", get_requested_pagename () );
		$this->cleanUp ();
	}
	public function testGetRequestedPageNameWithoutSystemName() {
		$this->cleanUp ();
		$this->assertEquals ( get_frontpage (), get_requested_pagename () );
	}
	public function testGetRequestedPageNameWithNull() {
		$_GET ["seite"] = null;
		$this->assertEquals ( get_frontpage (), get_requested_pagename () );
	}
	public function testGetRequestedPageNameWithEmptyString() {
		$_GET ["seite"] = "";
		$this->assertEquals ( get_frontpage (), get_requested_pagename () );
	}
	public function testIsHomeTrue() {
		$_GET ["seite"] = get_frontpage ();
		$this->assertTrue ( is_home () );
		$this->cleanUp ();
	}
	public function testIsHomeFalse() {
		$_GET ["seite"] = "nothome";
		$this->assertFalse ( is_home () );
		$this->cleanUp ();
	}
	public function testIsFrontPageTrue() {
		$_GET ["seite"] = get_frontpage ();
		$this->assertTrue ( is_frontpage () );
		$this->cleanUp ();
	}
	public function testIsFrontPageFalse() {
		$_GET ["seite"] = "nothome";
		$this->assertFalse ( is_frontpage () );
		$this->cleanUp ();
	}
}