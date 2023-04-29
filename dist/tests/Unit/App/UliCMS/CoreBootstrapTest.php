<?php

use App\Helpers\DateTimeHelper;
use App\Registries\LoggerRegistry;
use App\Storages\Vars;
use App\UliCMS\CoreBootstrap;
use App\Utils\Logger;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

class CoreBootstraptest extends TestCase {
    protected function setUp(): void {
        FileSystem::delete(ULICMS_TMP);
    }

    protected function tearDown(): void {
        FileSystem::createDir(ULICMS_TMP);
    }

    public function testSetExceptionHandler() {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);

        $this->assertNull($coreBootstrap->setExceptionHandler());
    }

    public function testCheckConfigExists(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertTrue($coreBootstrap->checkConfigExists());
    }

    public function testGetInstallerUrl(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertStringStartsWith('./installer', $coreBootstrap->getInstallerUrl());
    }

    public function testIsFreshDeploy(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertFalse($coreBootstrap->isFreshDeploy());
    }

    public function testPostDeployUpdate(): void {
        Settings::delete('initialized');
        $this->assertNull(Settings::get('initialized'));

        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->postDeployUpdate();

        $this->assertIsNumeric(Settings::get('initialized'));
    }

    public function testInitStorages(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->initStorages();

        $this->assertCount(0, Vars::get('http_headers'));
    }

    public function testLoadEnvFile(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->loadEnvFile();

        $this->assertIsInt(umask());
        $this->assertIsBool(is_debug_mode());
    }

    public function testShouldRedirectToSSL(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);

        $this->assertFalse($coreBootstrap->shouldRedirectToSSL());
    }

    public function testInitLoggers(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->initLoggers();

        $loggers = LoggerRegistry::getAll();

        $this->assertIsArray($loggers);

        foreach($loggers as $logger) {
            $this->assertInstanceOf(Logger::class, $logger);
        }
    }

    public function testDefinePathConstants(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->definePathConstants();

        $this->assertDirectoryExists(ULICMS_CONTENT);
    }

    public function testInitLocale(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->initLocale();

        $this->assertTrue(DateTimeHelper::isValidTimezone(date_default_timezone_get()));
    }

    public function testCreateDirectories(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $coreBootstrap->createDirectories();

        $this->assertDirectoryExists(ULICMS_TMP);
        $this->assertFileExists(Path::resolve('ULICMS_GENERATED_PRIVATE/.htaccess'));
    }

    public function testIsAutomigrateEnabled(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertIsBool($coreBootstrap->isAutomigrateEnabled());
    }

    public function testSelectDatabase(): void {
        $coreBootstrap = new CoreBootstrap(ULICMS_ROOT);
        $this->assertTrue($coreBootstrap->selectDatabase());
    }
}
