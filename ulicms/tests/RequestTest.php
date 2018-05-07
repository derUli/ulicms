<?php
class RequestTest extends PHPUnit_Framework_TestCase {
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
	public function testGetMethod() {
		$_SERVER ["REQUEST_METHOD"] = "GET";
		$this->assertEquals ( "get", Request::getMethod () );
		$this->assertTrue ( Request::isGet () );
		$this->assertFalse ( Request::isPost () );
		$this->assertFalse ( Request::isHead () );
		$_SERVER ["REQUEST_METHOD"] = "POST";
		$this->assertEquals ( "post", Request::getMethod () );
		$this->assertFalse ( Request::isGet () );
		$this->assertTrue ( Request::isPost () );
		$this->assertFalse ( Request::isHead () );
		$_SERVER ["REQUEST_METHOD"] = "HEAD";
		$this->assertEquals ( "head", Request::getMethod () );
		$this->assertFalse ( Request::isGet () );
		$this->assertFalse ( Request::isPost () );
		$this->assertTrue ( Request::isHead () );
	}
	
	public function testIsAjaxRequest() {
		unset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] );
		$this->assertFalse ( Request::isAjaxRequest () );
		$_SERVER ['HTTP_X_REQUESTED_WITH'] = "XMLHttpRequest";
		$this->assertTrue ( Request::isAjaxRequest () );
		unset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] );
	}
}


