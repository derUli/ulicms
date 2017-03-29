<?php
class RequestClassTest extends PHPUnit_Framework_TestCase {
	public function testGetVar() {
		$_POST ["var1"] = "this";
		$_GET ["var1"] = "that";
		$_GET ["var2"] = "123";
		$_POST ["var3"] = "1.5";
		$this->assertEquals ( "this", Request::getVar ( "var1" ) );
		$this->assertEquals ( "this", Request::getVar ( "var1" ) );
		$this->assertEquals ( null, Request::getVar ( "nothing" ) );
		$this->assertEquals ( "not text", Request::getVar ( "nothing", "not text" ) );
		
		$this->assertEquals ( 0, Request::getVar ( "var1", null, "int" ) );
		$this->assertEquals ( 0.0, Request::getVar ( "var1", null, "float" ) );
		
		$this->assertEquals ( 123.0, Request::getVar ( "var2", null, "float" ) );
		$this->assertEquals ( 1, Request::getVar ( "var3", null, "int" ) );
	}
}


