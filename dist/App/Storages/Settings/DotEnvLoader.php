<?php


declare(strict_types=1);

namespace App\Storages\Settings;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Exceptions\FileNotFoundException;
use Dotenv\Dotenv;
use Path;

/**
 * Loads a .env configuration file
 */
class DotEnvLoader
{
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
     * @param string $environment ENVIRONMENT name
     *
     * @return self
     **/
    public static function fromEnvironment(string $dir, string $environment): self {
        $file = $environment === static::DEFAULT_ENVIRONMENT ? '.env' : ".env.{$environment}";

        static::checkExists($dir, $file);

        return new self($dir, $file);
    }

    /**
     * Load and validate dotenv file
     *
     * @return void
     */
    public function load(): void {
        static::checkExists($this->dir, $this->file);

        $dotenv = Dotenv::createImmutable($this->dir, $this->file);
        $dotenv->load();
        $this->validation($dotenv);
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

        $path = Path::resolve("{$dir}/{$file}");

        if(! is_file($file)){
            throw new FileNotFoundException("Environment file {$file} not found");
        }
    }
}
