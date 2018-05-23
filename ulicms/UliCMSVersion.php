<?php
if (! class_exists("UliCMSVersion")) {

    class UliCMSVersion
    {

        function __construct()
        {
            $this->version = "Gnampf";
            $this->releaseYear = 2018;
            $this->buildDate = 0; // {InsertBuildDate}
            $this->internalVersion = Array(
                2018,
                3,
                3
            );
            $this->update = "";
            $this->developmentVersion = false;
        }

        public function getReleaseYear()
        {
            return strval($this->releaseYear);
        }

        // Gibt den Codenamen der UliCMS Version zurück (z.B. 2013R2)
        public function getVersion()
        {
            return $this->version;
        }

        public function getUpdate()
        {
            return $this->update;
        }

        public function getDevelopmentVersion()
        {
            return $this->developmentVersion;
        }

        // Gibt die interne Versionsnummer als Array mit Integer-Datentyp zurück
        public function getInternalVersion()
        {
            return $this->internalVersion;
        }

        // Gibt die interne Versionsnummer als String mit Integer-Datentyp zurück
        public function getInternalVersionAsString()
        {
            return implode(".", $this->internalVersion);
        }

        public function getBuildTimestamp()
        {
            return $this->buildDate;
        }

        public function getBuildDate()
        {
            return strftime("%x %X", $this->getBuildTimestamp());
        }
    }
}

// For backwards compatiblity
class_alias("UliCMSVersion", "ulicms_version");