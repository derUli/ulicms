<?php
if (! class_exists ( "ulicms_version" )) {
	class ulicms_version {
		function __construct() {
			$this->version = "Big Ben";
			$this->releaseYear = 2015;
			$this->internalVersion = Array (
					9,
					0,
					2 
			);
			$this->update = "";
			$this->developmentVersion = true;
		}
		public function getReleaseYear() {
			return strval ( $this->releaseYear );
		}
		
		// Gibt den Codenamen der UliCMS Version zurück (z.B. 2013R2)
		public function getVersion() {
			return $this->version;
		}
		public function getUpdate() {
			return $this->update;
		}
		public function getDevelopmentVersion() {
			return $this->developmentVersion;
		}
		
		// Gibt die interne Versionsnummer als Array mit Integer-Datentyp zurück
		public function getInternalVersion() {
			return $this->internalVersion;
		}
	}
}
