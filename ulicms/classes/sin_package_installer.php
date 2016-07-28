<?php
class SinPackageInstaller {
	private $file = null;
	private $errors = array ();
	public function __construct($file) {
		if (isNotNullOrEmpty ( $file )) {
			$this->file = $file;
		}
	}
	private function loadPackage() {
		$data = file_get_contents ( $this->file );
		$json = json_decode ( $data, false );
		return $json;
	}
	public function getSize() {
		$data = $this->loadPackage ();
		$decoded = base64_decode ( $data ["data"] );
		$size = mb_strlen ( $decoded, '8bit' );
		return $size;
	}
	public function getProperty($name) {
		$data = $this->loadPackage ();
		return $data [$name];
	}
	public function getErrors() {
		return $this->errors;
	}
	public function isInstallable() {
		$this->errors = array ();
		$installed_modules = getAllModules ();
		$data = $this->loadPackage ();
		if (isset ( $data ["dependencies"] ) and is_aray ( $data ["dependencies"] )) {
			$dependencies = $data ["dependencies"];
			foreach ( $dependencies as $dependency ) {
				if (! in_array ( $dependency, $installed_modules )) {
					$this->errors [] = get_translation ( "dependecy_x_is_not_installed", array (
							$dependency 
					) );
				}
				$version = new ulicms_version ();
				$version->getInternalVersionAsString ();
				$version_not_supported = false;
				if (isNotNullOrEmpty ( $data ["compatible_from"] ) and is_string ( $data ["compatible_from"] )) {
					if (! version_compare ( $version, $data ["compatible_from"], ">=" )) {
						$version_not_supported = true;
					}
				}
				
				if (isNotNullOrEmpty ( $data ["compatible_to"] ) and is_string ( $data ["compatible_to"] )) {
					if (! version_compare ( $version, $data ["compatible_to"], "<=" )) {
						$version_not_supported = true;
					}
				}
				if ($version_not_supported) {
					$this->errors [] = get_translation ( "this_ulicms_version_is_not_supported" );
				}
			}
		}
		return (count ( $this->errors ) <= 0);
	}
}