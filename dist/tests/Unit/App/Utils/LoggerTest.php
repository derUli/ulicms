<?php

use App\Registries\LoggerRegistry;
use App\Utils\Logger;

class LoggerTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        sureRemoveDir(\App\Utils\Path::resolve('ULICMS_LOG/test_log'), true);
    }

    protected function tearDown(): void {
        sureRemoveDir(\App\Utils\Path::resolve('ULICMS_LOG/test_log'), true);
    }

    public function testRegisterAndUnregisterLogger(): void {
        $logger = new Logger(\App\Utils\Path::resolve('ULICMS_LOG/test_log'));
        $this->assertTrue(is_dir(\App\Utils\Path::resolve('ULICMS_LOG/test_log')));
        LoggerRegistry::register('test_log', $logger);
        $this->assertInstanceOf(Logger::class, LoggerRegistry::get('test_log'));

        LoggerRegistry::unregister('test_log');
        $this->assertNull(LoggerRegistry::get('test_log'));
    }

    public function testLogFolderIsProtected(): void {
        $htaccessFile = \App\Utils\Path::resolve('ULICMS_LOG/.htaccess');
        $this->assertTrue(is_file($htaccessFile));
        $this->assertContains('deny from all', array_map('strtolower', \App\Helpers\StringHelper::linesFromFile($htaccessFile)));
    }

    public function testGetPath(): void {
        $logger = new Logger(\App\Utils\Path::resolve('ULICMS_LOG/test_log'));

        LoggerRegistry::register('test_log', $logger);

        $this->assertStringContainsString('content/log/test_log', $logger->getPath());

        LoggerRegistry::unregister('test_log');
    }

    public function testLogDebug(): void {
        $logger = new Logger(\App\Utils\Path::resolve('ULICMS_LOG/test_log'));

        LoggerRegistry::register('test_log', $logger);

        $file = \App\Utils\Path::resolve('ULICMS_LOG/test_log/' . get_environment() . '_' . date('Y-m-d') . '.log');

        $log_test_token = 'Test ' . uniqid();
        $logger->debug($log_test_token);
        $this->assertFileExists($file);

        $file_content = file_get_contents($file);

        $expected = "[debug] {$log_test_token}";
        $this->assertStringContainsString($expected, $file_content);

        LoggerRegistry::unregister('test_log');
    }

    public function testLogError(): void {
        $logger = new Logger(\App\Utils\Path::resolve('ULICMS_LOG/test_log'));

        LoggerRegistry::register('test_log', $logger);

        $file = \App\Utils\Path::resolve('ULICMS_LOG/test_log/' . get_environment() . '_' . date('Y-m-d') . '.log');

        $log_test_token = 'Test ' . uniqid();
        $logger->error($log_test_token);
        $this->assertFileExists($file);

        $file_content = file_get_contents($file);

        $expected = "[error] {$log_test_token}";
        $this->assertStringContainsString($expected, $file_content);

        LoggerRegistry::unregister('test_log');
    }

    public function testLogInfo(): void {
        $logger = new Logger(\App\Utils\Path::resolve('ULICMS_LOG/test_log'));

        LoggerRegistry::register('test_log', $logger);

        $file = \App\Utils\Path::resolve('ULICMS_LOG/test_log/' . get_environment() . '_' . date('Y-m-d') . '.log');

        $log_test_token = 'Test ' . uniqid();
        $logger->info($log_test_token);

        $this->assertFileExists($file);

        $file_content = file_get_contents($file);

        $expected = "[info] {$log_test_token}";
        $this->assertStringContainsString($expected, $file_content);

        LoggerRegistry::unregister('test_log');
    }
}
