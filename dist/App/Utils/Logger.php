<?php

declare(strict_types=1);

namespace App\Utils;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use CMSConfig;

// Wrapper for KLogger
class Logger
{
    private $path;

    private $logger;

    public function __construct(string $path, ?CMSConfig $cmsConfig = null)
    {
        $cfg = $cmsConfig ?: new CMSConfig();
        $environment = get_environment();
        $this->path = $path;
        // if the directory doesn't exist, create it.
        if (! is_dir($this->path)) {
            @mkdir($path, 0777, true);
        }
        if (is_dir($this->path)) {
            $this->logger = new \Katzgrau\KLogger\Logger(
                $this->path,
                \Psr\Log\LogLevel::DEBUG,
                [
                    'extension' => 'log',
                    'prefix' => "{$environment}_"
                ]
            );
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function debug($message): void
    {
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }

    public function error($message): void
    {
        if ($this->logger) {
            $this->logger->error($message);
        }
    }

    public function info($message): void
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }
}
