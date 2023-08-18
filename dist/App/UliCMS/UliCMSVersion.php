<?php

declare(strict_types=1);

namespace App\UliCMS;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\DateTimeHelper;

/**
 * Provides information about the UliCMS release version
 */
class UliCMSVersion {
    public const CODE_NAME = 'Gnawing Viscacha';

    public const RELEASE_YEAR = 2024;

    public const BUILD_DATE = 0; // {InsertBuildDate}

    public const VERSION_NUMBER = [2024, 1];

    /**
     * Returns the release year
     * @return string
     */
    public function getReleaseYear(): string {
        return (string)static::RELEASE_YEAR;
    }

    /**
     * Returns the release Codename
     * @return string
     */
    public function getCodeName(): string {
        return static::CODE_NAME;
    }

    /**
     * Returns the full number as array
     * @return int[]
     */
    public function getInternalVersion(): array {
        return static::VERSION_NUMBER;
    }

    /**
     * Returns the full version number as formatted string
     * @return string
     */
    public function getInternalVersionAsString(): string {
        return implode('.', static::VERSION_NUMBER);
    }

    /**
     * Returns the Unix timestamp when the release was built
     *
     * @return int
     */
    public function getBuildTimestamp(): int {
        return static::BUILD_DATE;
    }

    /**
     * Returns the formatted date when the release was built.
     * @return string
     */
    public function getBuildDate(): string {
        return DateTimeHelper::timestampToFormattedDateTime($this->getBuildTimestamp()) ?? '';
    }
}
