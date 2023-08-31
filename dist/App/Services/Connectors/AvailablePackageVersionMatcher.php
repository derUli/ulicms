<?php

namespace App\Services\Connectors;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\ArrayHelper;

use function is_version_number;

class AvailablePackageVersionMatcher {
    private $versionData = [];

    /**
     * Constructor
     * @param $versionData
     */
    public function __construct($versionData = null) {
        $this->loadData($versionData);
    }

    public function loadData($versionData): void {
        $this->versionData = [];
        if ($versionData === null) {
            return;
        }

        $parsedVersionData = is_string($versionData) ?
                json_decode($versionData, true) : $versionData;

        $versions = $parsedVersionData['versions'] ?? [];

        foreach ($versions as $version) {
            if (ArrayHelper::hasMultipleKeys(
                $version,
                [
                    'version',
                    'compatible_with',
                    'file'
                ]
            ) &&
                    is_version_number($version['version']) &&
                    is_version_number($version['compatible_with'])
            ) {
                $this->versionData[] = $version;
            }
        }
    }

    public function getAllVersions(): array {
        $releases = $this->versionData;

        usort($releases, static function($a, $b) {
            return \App\Utils\VersionComparison::compare(
                $a['version'],
                $b['version'],
                '<'
            ) ? 1 : 0;
        });
        usort($releases, static function($a, $b) {
            return \App\Utils\VersionComparison::compare(
                $a['compatible_with'],
                $b['compatible_with'],
                '<'
            ) ? 1 : 0;
        });

        return $releases;
    }

    public function getCompatibleVersions(?string $ulicmsVersion = null): array {
        $allReleases = $this->getAllVersions();
        $suitableReleases = [];

        foreach ($allReleases as $release) {
            $compatible = \App\Utils\VersionComparison::compare(
                $release['compatible_with'],
                $ulicmsVersion,
                '>='
            );

            if ($compatible) {
                $suitableReleases[] = $release;
            }
        }
        if (! count($suitableReleases)) {
            $suitableReleases = $allReleases;
        }

        return $suitableReleases;
    }
}
