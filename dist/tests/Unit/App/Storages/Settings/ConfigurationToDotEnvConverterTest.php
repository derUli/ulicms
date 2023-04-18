<?php

declare(strict_types=1);

use App\Storages\Settings\ConfigurationToDotEnvConverter;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ConfigurationToDotEnvConverterTest extends TestCase
{
    use MatchesSnapshots;

    public function testConvertToArray(): void {
        $config = $this->getCMSConfig();

        $converter = new ConfigurationToDotEnvConverter($config);

        $actual = $converter->convertToArray();

        $this->assertEquals('myUser', $actual['DB_USER']);
        $this->assertEquals('true', $actual['DEBUG']);
        $this->assertEquals('false', $actual['EXCEPTION_LOGGING']);
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

   protected function getCMSConfig(): CMSConfig {
        $config = new CMSConfig();

        $rc = new \ReflectionClass($config);

        //get all the public properties
        $properties = $rc->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach($properties as $p) {
            $attribute = $p->getName();

            if(! is_bool($config->{$attribute})){
                $config->{$attribute} = null;
            }
        }

        $config->db_user = 'myUser';
        $config->debug = true;
        $config->exception_logging = false;

        return $config;
   }
}
