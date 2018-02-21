<?php
class LoggerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		SureRemoveDir ( Path::resolve ( "ULICMS_LOG/test_log" ), true );
	}
	public function tearDown() {
		SureRemoveDir ( Path::resolve ( "ULICMS_LOG/test_log" ), true );
	}
	public function testRegisterAndUnregisterLogger() {
		$logger = new Logger ( Path::resolve ( "ULICMS_LOG/test_log" ) );
		$this->assertTrue ( is_dir ( Path::resolve ( "ULICMS_LOG/test_log" ) ) );
		LoggerRegistry::register ( "test_log", $logger );
		$this->assertInstanceOf ( "Logger", LoggerRegistry::get ( "test_log" ) );
		
		LoggerRegistry::unregister ( "test_log" );
		$this->assertNull ( LoggerRegistry::get ( "test_log" ) );
	}
	// TODO: Testfall implementieren, der mit info(), debug() und error() loggt.
	public function testLogFolderIsProtected() {
		$this->assertTrue ( file_exists ( Path::resolve ( "ULICMS_LOG/.htaccess" ) ) );
		$this->assertContains ( "deny from all", array_map ( "strtolower", StringHelper::linesFromFile ( Path::resolve ( "ULICMS_LOG/.htaccess" ) ) ) );
	}
}