<?php

namespace UliCMS\Services\Connectors\eXtend;

use function is_version_number;
use UliCMS\Helpers\ArrayHelper;
use UliCMSVersion;

class AvailablePackageVersionMatcher {

    private $versionData = [];

    public function __construct($versionData = null) {
        $this->loadData($versionData);
    }
    
    public function loadData($versionData) {
        $this->versionData = [];
        if ($versionData === null) {
            return;
        }

        $parsedVersionData = is_string($versionData) ?
                json_decode($versionData, true) : $versionData;

        $versions = is_array($parsedVersionData["versions"]) ?
                $parsedVersionData["versions"] : [];

        foreach ($versions as $version) {
            if (ArrayHelper::hasMultipleKays(
                            $version,
                            [
                                "version",
                                "compatible_with",
                                "file"
                            ]
                    ) and
                    is_version_number($version["version"]) and
                    is_version_number($version["compatible_with"])
            ) {
                $this->versionData[] = $version;
            }
        }
    }
    public function getAllVersions(): array{
        $releases = $this->versionData;
        $ulicmsVersionInfo = new UliCMSVersion();

        $ulicmsVersion = $ulicmsVersion ?
                $ulicmsVersion : $ulicmsVersionInfo->getInternalVersionAsString();

        usort($releases, function($a, $b) {
            return version_compare($a["version"], $b["version"], "<");
        });
        usort($releases, function($a, $b) {
            return version_compare($a["compatible_with"], $b["compatible_with"], "<");
        });

       return $releases;
    }

    public function getCompatibleVersions(?string $ulicmsVersion = null): array {
        $releases = $this->getAllVersions();
        $suitableReleases = [];

        foreach ($releases as $release) {
            $compatible = version_compare(
                    $release["compatible_with"],
                    $ulicmsVersion,
                    ">="
            );
            if ($compatible) {
                $suitableReleases[] = $release;
            }
        }
        

        return $suitableReleases;
    }

}
