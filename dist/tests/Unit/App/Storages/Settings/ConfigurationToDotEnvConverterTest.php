<?php

declare(strict_types=1);

use App\Storages\Settings\ConfigurationToDotEnvConverter;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ConfigurationToDotEnvConverterTest extends TestCase
{
    use MatchesSnapshots;

    private string $envFile;

    protected function setUp(): void {
        parent::setUp();

        $this->envFile = Path::resolve('ULICMS_ROOT/.env.foobar');
    }

    protected function tearDown(): void {
        FileSystem::delete($this->envFile);

        parent::tearDown();
    }

    public function testConvertToArray(): void {
        $config = $this->getCMSConfig();

        $converter = new ConfigurationToDotEnvConverter($config);

        $actual = $converter->convertToArray();

        $this->assertEquals('myUser', $actual['DB_USER']);
        $this->assertEquals('true', $actual['DEBUG']);
        $this->assertEquals('false', $actual['EXCEPTION_LOGGING']);
        $this->assertEquals('foo; bar', $actual['AN_ARRAY']);
    }

    public function testConvertToString(): void {
        $config = $this->getCMSConfig();

        $converter = new ConfigurationToDotEnvConverter($config);

        $actual = $converter->convertToString();

        $this->assertMatchesTextSnapshot($actual);
    }

    public function testToString(): void {
        $config = $this->getCMSConfig();

        $converter = new ConfigurationToDotEnvConverter($config);

        $this->assertMatchesTextSnapshot((string)$converter);
    }

    public function testWriteEnvFile() {
        $config = $this->getCMSConfig();
        $converter = new ConfigurationToDotEnvConverter($config);
        $targetEnv = 'foobar';

        $this->assertTrue($converter->writeEnvFile(false, 'foobar'));
        $this->assertMatchesFileSnapshot($this->envFile);

        $this->assertFalse($converter->writeEnvFile(false, 'foobar'));
        $this->assertMatchesFileSnapshot($this->envFile);
    }

    public function testWriteEnvFileOverwrite() {

        $config = $this->getCMSConfig();
        $converter = new ConfigurationToDotEnvConverter($config);
        $targetEnv = 'foobar';

        $this->assertTrue($converter->writeEnvFile(true, 'foobar'));
        $this->assertMatchesFileSnapshot($this->envFile);

        $this->assertTrue($converter->writeEnvFile(true, 'foobar'));
        $this->assertMatchesFileSnapshot($this->envFile);
    }

   protected function getCMSConfig(): CMSConfig {

        require_once ULICMS_ROOT . '/tests/fixtures/CMSConfig.php';
        $config = new CMSConfig();

        $rc = new \ReflectionClass($config);

        $config->db_user = 'myUser';
        $config->debug = true;
        $config->exception_logging = false;
        $config->dbmigrator_drop_database_on_shutdown = true;

        return $config;
   }
}
