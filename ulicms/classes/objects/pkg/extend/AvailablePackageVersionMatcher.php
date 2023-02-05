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
            if (ArrayHelper::hasMultipleKeys(
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

    public function getAllVersions(): array {
        $releases = $this->versionData;

        usort($releases, function ($a, $b) {
            return \UliCMS\Utils\VersionComparison\compare(
                    $a["version"], $b["version"], "<"
            ) ? 1 : 0;
        });
        usort($releases, function ($a, $b) {
            return \UliCMS\Utils\VersionComparison\compare(
                    $a["compatible_with"], $b["compatible_with"], "<"
            ) ? 1 : 0;
        });

        return $releases;
    }

    public function getCompatibleVersions(?string $ulicmsVersion = null): array {
        $allReleases = $this->getAllVersions();
        $suitableReleases = [];

        foreach ($allReleases as $release) {
            $compatible = \UliCMS\Utils\VersionComparison\compare(
                    $release["compatible_with"],
                    $ulicmsVersion,
                    ">="
            );

            if ($compatible) {
                $suitableReleases[] = $release;
            }
        }
        if (!count($suitableReleases)) {
            $suitableReleases = $allReleases;
        }

        return $suitableReleases;
    }

}
