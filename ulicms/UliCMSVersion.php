<?php

declare(strict_types=1);

class UliCMSVersion {

    function __construct() {
        $this->version = "Tidy Guanako";
        $this->releaseYear = 2019;
        $this->buildDate = 0; // {InsertBuildDate}
        $this->internalVersion = Array(
            2019,
            4,
			0,
			1
        );
        $this->update = "";
        $this->developmentVersion = false;
    }

    public function getReleaseYear(): string {
        return strval($this->releaseYear);
    }

    // Gibt den Codenamen der UliCMS Version zurück (z.B. 2013R2)
    public function getVersion(): string {
        return $this->version;
    }

    public function getUpdate(): string {
        return $this->update;
    }

    public function getDevelopmentVersion(): bool {
        return $this->developmentVersion;
    }

    // Gibt die interne Versionsnummer als Array mit Integer-Datentyp zurück
    public function getInternalVersion(): array {
        return $this->internalVersion;
    }

    // Gibt die interne Versionsnummer als String mit Integer-Datentyp zurück
    public function getInternalVersionAsString(): string {
        return implode(".", $this->internalVersion);
    }

    public function getBuildTimestamp(): int {
        return $this->buildDate;
    }

    public function getBuildDate(): string {
        return strftime("%x %X", $this->getBuildTimestamp());
    }

}

// For backwards compatiblity
class_alias("UliCMSVersion", "ulicms_version");
