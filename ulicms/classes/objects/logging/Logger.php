<?php
class Logger {
	private $path;
	private $enabled = false;
	private $logger;
	public function __construct($path) {
		$this->path = $path;
		
		if (! is_dir ( $this->path )) {
			@mkdir ( $path, 0777, true );
		}
		if (is_dir ( $this->path )) {
			$this->logger = new Katzgrau\KLogger\Logger ( $this->path );
		}
	}
	public function debug($message) {
		if ($this->logger) {
			$this->logger->debug ( $message );
		}
	}
	public function error($message) {
		if ($this->logger) {
			$this->logger->error ( $message );
		}
	}
	public function info($message) {
		if ($this->logger) {
			$this->logger->info ( $message );
		}
	}
}