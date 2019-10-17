<?php

declare(strict_types=1);

class UliCMSVersion {

    function __construct() {
        $this->codename = "Lurking Anaconda";
        $this->releaseYear = 2020;
        $this->buildDate = 0; // {InsertBuildDate}
        $this->internalVersion = [
            2020,
            1
        ];
        $this->update = "";
    }

    public function getReleaseYear(): string {
        return strval($this->releaseYear);
    }

    //  returns the codename of this UliCMS release
    public function getCodeName(): string {
        return $this->codename;
    }

    // returns the version number
    public function getInternalVersion(): array {
        return $this->internalVersion;
    }

    // Returns the full version number as string
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
