<?php
if (! class_exists ( "ulicms_version" )) {
	class ulicms_version {
		function __construct() {
			$this->version = "Sockenreißer";
			$this->releaseYear = 2018;
			$this->internalVersion = Array (
					2018,
					1
					
			);
			$this->update = "";
			$this->developmentVersion = false;
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
		// Gibt die interne Versionsnummer als String mit Integer-Datentyp zurück
		public function getInternalVersionAsString() {
			return implode ( ".", $this->internalVersion );
		}
	}
}
