<?php

use UliCMS\Helpers\TestHelper;

class LoggerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        sureRemoveDir(Path::resolve("ULICMS_LOG/test_log"), true);
    }

    protected function tearDown(): void
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
        $this->assertTrue(file_exists($htaccessFile));
        $this->assertContains("deny from all", array_map("strtolower", StringHelper::linesFromFile($htaccessFile)));
    }

    public function testLogDebug()
    {
        $logger = new Logger(Path::resolve("ULICMS_LOG/test_log"));

        LoggerRegistry::register("test_log", $logger);

        $file = Path::resolve("ULICMS_LOG/test_log/" . get_environment() . "_" . date("Y-m-d") . ".log");

        $log_test_token = "Test " . uniqid();
        $logger->debug($log_test_token);
        $this->assertFileExists($file);

        $file_content = file_get_contents($file);

        $expected = "[debug] {$log_test_token}";
        $this->assertStringContainsString($expected, $file_content);

        LoggerRegistry::unregister("test_log");
    }

    public function testLogError()
    {
        $logger = new Logger(Path::resolve("ULICMS_LOG/test_log"));

        LoggerRegistry::register("test_log", $logger);

        $file = Path::resolve("ULICMS_LOG/test_log/" . get_environment() . "_" . date("Y-m-d") . ".log");

        $log_test_token = "Test " . uniqid();
        $logger->error($log_test_token);
        $this->assertFileExists($file);

        $file_content = file_get_contents($file);

        $expected = "[error] {$log_test_token}";
        $this->assertStringContainsString($expected, $file_content);

        LoggerRegistry::unregister("test_log");
    }

    public function testLogInfo()
    {
        $logger = new Logger(Path::resolve("ULICMS_LOG/test_log"));

        LoggerRegistry::register("test_log", $logger);

        $file = Path::resolve("ULICMS_LOG/test_log/" . get_environment() . "_" . date("Y-m-d") . ".log");

        $log_test_token = "Test " . uniqid();
        $logger->info($log_test_token);

        $this->assertFileExists($file);

        $file_content = file_get_contents($file);

        $expected = "[info] {$log_test_token}";
        $this->assertStringContainsString($expected, $file_content);

        LoggerRegistry::unregister("test_log");
    }

    public function testFixLogPermissions()
    {
        $cfg = new CMSConfig();
        $cfg->fix_log_permissions = true;
        
        $logger = new Logger(Path::resolve("ULICMS_LOG/test_log"), $cfg);

        $logger->debug("Test");

        clearstatcache();
        
        $path = $logger->getPath();
        
        if (TestHelper::isWindowsServer()) {
            $this->assertTrue(is_writable($path));
        } else {
            $this->assertEquals(0777, fileperms($path));
        }
    }
}
