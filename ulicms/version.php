<?php
class ulicms_version {
	function ulicms_version() {
		$this->version = "Mountain King";
		$this->internalVersion = Array (
				9,
				0,
				1,
				2
		);
		$this->update = "Refresh ".$this->internalVersion[3];
		$this->developmentVersion = false;
	}
	
	// Gibt den Codenamen der UliCMS Version zurück (z.B. 2013R2)
	function getVersion() {
		return $this->version;
	}
	function getUpdate() {
		return $this->update;
	}
	public function getVersionString(){
		$version = array_slice($this->getVersion(), 0, 3);
		$text = implode(".", $version);
		if(count($this->getVersion()) > 3){
			$v = $this->getVersion();
			$refresh = $v[4];
			$text = $text . " Refresh " . $refresh;
		}
		return $text;
	}

	function getDevelopmentVersion() {
		return $this->developmentVersion;
	}
	
	// Gibt die interne Versionsnummer als Array mit Integer-Datentyp zurück
	function getInternalVersion() {
		return $this->internalVersion;
	}
}
