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
	public function testGetStatusCodeByNumber() {
		$this->assertEquals ( "200 OK", Request::getStatusCodeByNumber ( 200 ) );
		$this->assertEquals ( "301 Moved Permanently", Request::getStatusCodeByNumber ( 301 ) );
		$this->assertEquals ( "302 Found", Request::getStatusCodeByNumber ( 302 ) );
		$this->assertEquals ( "401 Unauthorized", Request::getStatusCodeByNumber ( 401 ) );
		$this->assertEquals ( "403 Forbidden", Request::getStatusCodeByNumber ( 403 ) );
		$this->assertEquals ( "404 Not Found", Request::getStatusCodeByNumber ( 404 ) );
		$this->assertEquals ( '418 I\'m a teapot', Request::getStatusCodeByNumber ( 418 ) );
	}
}


