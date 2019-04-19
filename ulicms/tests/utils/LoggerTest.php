<?php

class LoggerTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        sureRemoveDir(Path::resolve("ULICMS_LOG/test_log"), true);
    }

    public function tearDown()
    {
        sureRemoveDir(Path::resolve("ULICMS_LOG/test_log"), true);
    }

    public function testRegisterAndUnregisterLogger()
    {
        $logger = new Logger(Path::resolve("ULICMS_LOG/test_log"));
        $this->assertTrue(is_dir(Path::resolve("ULICMS_LOG/test_log")));
        LoggerRegistry::register("test_log", $logger);
        $this->assertInstanceOf("Logger", LoggerRegistry::get("test_log"));
        
        LoggerRegistry::unregister("test_log");
        $this->assertNull(LoggerRegistry::get("test_log"));
    }

    public function testLogFolderIsProtected()
    {
        $htaccessFile = Path::resolve("ULICMS_LOG/.htaccess");
        $this->assertTrue(is_file($htaccessFile));
        $this->assertContains("deny from all", array_map("strtolower", StringHelper::linesFromFile($htaccessFile)));
    }
    // TODO: Testfall implementieren, der mit info(), debug() und error() loggt.
}