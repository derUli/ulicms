<?php

declare(strict_types=1);

namespace App\Storages\Settings;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Exceptions\FileNotFoundException;
use Dotenv\Dotenv;

/**
 * Loads a .env configuration file
 */
class DotEnvLoader {
    public const DEFAULT_ENVIRONMENT = 'default';

    private string $dir;

    private string $file;

    /**
     * Constructor
     *
     * @param string $dir Directory name
     * @param string $file Filename
     */
    public function __construct(string $dir, string $file = '.env') {
        $this->dir = $dir;
        $this->file = $file;
    }

    /**
     * Initialize dotenv loader from environment
     *
     * @param string $dir Directory name
     * @param string $environment Environment name
     *
     * @return self
     **/
    public static function fromEnvironment(string $dir, string $environment): self {
        $file = static::envFilenameFromEnvironment($environment);


        static::checkExists($dir, $file);

        return new static($dir, $file);
    }

    /**
     * Gets .env filename from environment name
     *
     * @param string $environment Environment name
     *
     * @return string
     */
    public static function envFilenameFromEnvironment(string $environment): string {
        return $environment === static::DEFAULT_ENVIRONMENT ? '.env' : ".env.{$environment}";
    }

    /**
     * Load and validate dotenv file
     *
     * @return void
     */
    public function load(): void {
        static::checkExists($this->dir, $this->file);

        $dotenv = Dotenv::createMutable($this->dir, $this->file);
        $dotenv->load();
        $this->validation($dotenv);

        foreach($_ENV as $key => $value) {

            // Convert booleans since dotenv validation does no type castings
            if($value === 'true' || $value === 'false') {
                $value = $value === 'true';
            }

            $_ENV[$key] = $value;
        }
    }

    /**
     * Validates the .env file
     *
     * @return void
     */
    public function validation(DotEnv $dotenv): void {
        // App Environment
         $dotenv->required('APP_ENV')->notEmpty();

         // Database stuff
         $dotenv->required('DB_SERVER')->notEmpty();
         $dotenv->required('DB_USER')->notEmpty();
         $dotenv->required('DB_PASSWORD');
         $dotenv->required('DB_DATABASE')->notEmpty();
         $dotenv->required('DB_PREFIX')->notEmpty();

         // Debugging stuff
         $dotenv->required('DEBUG')->isBoolean();
         $dotenv->required('EXCEPTION_LOGGING')->isBoolean();

         // CMS defaults stuff
         $dotenv->required('DEFAULT_CONTENT_TYPE');
         $dotenv->required('DEFAULT_MENU')->notEmpty();
    }

    protected static function checkExists(string $dir, string $file): void {
        $path = "{$dir}/{$file}";

        if(! is_file($path)) {
            throw new FileNotFoundException("Environment file {$file} not found");
        }
    }
}
