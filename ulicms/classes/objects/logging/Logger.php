<?php

// Wrapper for KLogger
class Logger {

    private $path;
    private $enabled = false;
    private $logger;

    public function __construct($path) {
        $this->path = $path;
        // if the directory doesn't exist, create it.
        if (!is_dir($this->path)) {
            @mkdir($path, 0777, true);
        }
        if (is_dir($this->path)) {
            $this->logger = new Katzgrau\KLogger\Logger($this->path, Psr\Log\LogLevel::DEBUG, array(
                "extension" => "log"
            ));
            $cfg = new CMSConfig();
            // Option fix_log_permissions
            if (is_true($cfg->fix_log_permissions)) {
                $files = glob($this->path . "/log_*.log");
                foreach ($files as $file) {
                    @chmod($file, 0777);
                }
            }
        }
    }

    public function debug($message) {
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }

    public function error($message) {
        if ($this->logger) {
            $this->logger->error($message);
        }
    }

    public function info($message) {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }

}
