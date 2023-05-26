<?php

declare(strict_types=1);

namespace App\Storages\Settings;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use CMSConfig;
use Nette\Utils\FileSystem;

/**
 * Converts old configuration files to dotenv
 */
class ConfigurationToDotEnvConverter {
    /**
     * @var CMSConfig
     */
    private CMSConfig $config;

    /**
     * Constructor
     *
     * @param CMSConfig $config
     */
    public function __construct(CMSConfig $config) {
        $this->config = $config;
    }

    /**
     * Call convertToString on typecast
     *
     * @return string
     */
    public function __toString(): string {
        return $this->convertToString();
    }

    /**
     * Converts the CMSConfig to a key value pair
     *
     * @return array<string, string|bool|int>
     */
    public function convertToArray(): array {

        $convertedProperties = [];
        $cfg = $this->config;

        $rc = new \ReflectionClass($cfg);

        // Get the name and value of each of the public properties.
        $properties = $rc->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach($properties as $p) {
            $attribute = $p->getName();
            $value = $cfg->{$attribute};

            // Handle values
            if(is_array($value)) {
                $value = implode('; ', $value);
            } else {
                $value = ! is_bool($value) ? (string)$value : strbool($value);
            }

            $key = strtoupper($attribute);
            $convertedProperties[$key] = $value;
        }

        return $convertedProperties;
    }

    /**
     * Converts the CMSConfig to a .env style string
     *
     * @return string
     */
    public function convertToString(): string {
        $output = '';

        $attributes = $this->convertToArray();

        foreach($attributes as $key => $value) {
            $output .= "{$key}={$value}" . PHP_EOL;

        }

        return $output;
    }

    /**
     * writes env file
     *
     * @param bool $overwrite Overwrite file if it already exists
     * @param ?string $environment
     *
     * @return bool
     */
    public function writeEnvFile(bool $overwrite = false, ?string $environment = null): bool {
        $environment = $environment ?? get_environment();

        $dir = ULICMS_ROOT;
        $envFile = DotEnvLoader::envFilenameFromEnvironment($environment);

        $envFilePath = "{$dir}/{$envFile}";

        if(is_file($envFilePath) && ! $overwrite) {
            return false;
        }

        $envFileContend = $this->convertToString();

        FileSystem::write($envFile, $envFileContend);

        return is_file($envFile);
    }
}
