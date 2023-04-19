<?php

declare(strict_types=1);

use App\Exceptions\FileNotFoundException;
use App\Storages\Settings\DotEnvLoader;
use PHPUnit\Framework\TestCase;

class DotEnvLoaderTest extends TestCase
{
    public function testLoad(): void {
        $loader = new DotEnvLoader(ULICMS_ROOT, '.env.example');
        $loader->load();

        $this->assertEquals('mysql_host', $_ENV['DB_SERVER']);
        $this->assertEquals('example', $_ENV['APP_ENV']);
        $this->assertEquals('mysql_host', $_ENV['DB_SERVER']);
        $this->assertEquals('mysql_user', $_ENV['DB_USER']);
        $this->assertEquals('mysql_password', $_ENV['DB_PASSWORD']);
        $this->assertEquals('mysql_database', $_ENV['DB_DATABASE']);
        $this->assertEquals('prefix', $_ENV['DB_PREFIX']);
        $this->assertFalse($_ENV['DEBUG']);
        $this->assertTrue($_ENV['EXCEPTION_LOGGING']);
        $this->assertEquals('not_in_menu', $_ENV['DEFAULT_MENU']);
        $this->assertEquals('page', $_ENV['DEFAULT_CONTENT_TYPE']);
    }

    public function testFromEnvironmentSuccess(): void {
        $loader = DotEnvLoader::fromEnvironment(ULICMS_ROOT, 'example');
        $loader->load();

        $this->assertEquals('mysql_host', $_ENV['DB_SERVER']);
        $this->assertEquals('example', $_ENV['APP_ENV']);
        $this->assertEquals('mysql_host', $_ENV['DB_SERVER']);
        $this->assertEquals('mysql_user', $_ENV['DB_USER']);
        $this->assertEquals('mysql_password', $_ENV['DB_PASSWORD']);
        $this->assertEquals('mysql_database', $_ENV['DB_DATABASE']);
        $this->assertEquals('prefix', $_ENV['DB_PREFIX']);
        $this->assertFalse($_ENV['DEBUG']);
        $this->assertTrue($_ENV['EXCEPTION_LOGGING']);
        $this->assertEquals('not_in_menu', $_ENV['DEFAULT_MENU']);
        $this->assertEquals('page', $_ENV['DEFAULT_CONTENT_TYPE']);
    }

    public function testFromEnvironmentFails(): void {
        $this->expectException(FileNotFoundException::class);

        $loader = DotEnvLoader::fromEnvironment(ULICMS_ROOT, 'non_existing_environment');
        $loader->load();
    }

    public function testEnvFilenameFromEnvironmentDefault(): void {
        $this->assertEquals('.env', DotEnvLoader::envFilenameFromEnvironment(DotEnvLoader::DEFAULT_ENVIRONMENT));
    }

    public function testEnvFilenameFromEnvironmentOther(): void {
        $this->assertEquals('.env.foobar', DotEnvLoader::envFilenameFromEnvironment('foobar'));
    }
}
