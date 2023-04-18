<?php 
declare(strict_types=1);

namespace App\Storages\Settings;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use CMSConfig;

/**
 * Loads a .env configuration file
 */
class ConfigurationToDotEnvConverter
{
    private CMSConfig $config;

    public function __construct(CMSConfig $config){
        $this->config = $config;
    }

    public function convertToArray(): array {
        
        $convertedProperties = [];
        $cfg = $this->config;

        $rc = new \ReflectionClass($cfg);

        // Get the name and value of each of the public properties.
        $properties = $rc->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach($properties as $p) {

            $attribute = $p->getName();
            $value = !is_bool($cfg->$attribute) ? (string)$cfg->$attribute : strbool($cfg->$attribute);
            $key = strtoupper($attribute);
            $convertedProperties[$key] = $value;
        }

        return $convertedProperties;
    }

    public function convertToString(): string {
        
        $output = '';
        
        $attributes = $this->convertToArray();

        foreach($attributes as $key => $value){
            $output .= "{$key}={$value}" . PHP_EOL;
        }
        return $output;
    }

    public function __toString(): string{
        return $this->convertToString();
    }
}
