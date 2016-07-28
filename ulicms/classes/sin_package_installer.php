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
		$json = json_decode ( $data, true );
		return $json;
	}
	public function extractArchive() {
		$path = Path::resolve ( "ULICMS_TMP/package-" . $this->getProperty ( "id" ) . "-" . $this->getProperty ( "version" ) . ".tar.gz" );
		$data = $this->loadPackage ();
		$decoded = base64_decode ( $data ["data"] );
		file_put_contents ( $path, $decoded );
		return $path;
	}
	public function installPackage() {
		if ($this->isInstallable ()) {
			$path = $this->extractArchive ();
			$pkg = new PackageManager ();
			$result = $pkg->installPackage ( $path );
			unlink ( $path );
			return $result;
		} else {
			return false;
		}
	}
	public function getSize() {
		$data = $this->loadPackage ();
		$decoded = base64_decode ( $data ["data"] );
		$size = mb_strlen ( $decoded, '8bit' );
		return $size;
	}
	public function getProperty($name) {
		$data = $this->loadPackage ();
		if (isset ( $data [$name] ) and isNotNullOrEmpty ( $data [$name] )) {
			return $data [$name];
		} else {
			return null;
		}
	}
	public function getErrors() {
		return $this->errors;
	}
	public function isInstallable() {
		$this->errors = array ();
		$installed_modules = getAllModules ();
		$data = $this->loadPackage ();
		if (isset ( $data ["dependencies"] ) and is_array ( $data ["dependencies"] )) {
			$dependencies = $data ["dependencies"];
			foreach ( $dependencies as $dependency ) {
				if (! in_array ( $dependency, $installed_modules )) {
					$this->errors [] = get_translation ( "dependency_x_is_not_installed", array (
							"%x%" => $dependency 
					) );
				}
			}
				$version = new ulicms_version ();
				$version = $version->getInternalVersionAsString ();
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
				
				$decoded = base64_decode ( $data ["data"] );
				$sha_hash = sha1 ( $decoded );
				if ($sha_hash != $data ["checksum"]) {
					$this->errors [] = get_translation ( "sha1_checksum_not_equal" );
				}
			
		}
		return (count ( $this->errors ) <= 0);
	}
}