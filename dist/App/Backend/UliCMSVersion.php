<?php

declare(strict_types=1);

namespace App\Backend;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Helpers\DateTimeHelper;

/**
 * Provides information about the UliCMS release version
 */
class UliCMSVersion
{
    private string $codeName;

    private int $releaseYear;

    private int $buildDate;

    private array $internalVersion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->codeName = 'Beetle-Eating Nandu';
        $this->releaseYear = 2023;
        $this->buildDate = 0; // {InsertBuildDate}
        $this->internalVersion = [
            2023,
            3
        ];
    }

    /**
     * Returns the release year
     * @return string
     */
    public function getReleaseYear(): string
    {
        return (string)$this->releaseYear;
    }

    /**
     * Returns the release Codename
     * @return string
     */
    public function getCodeName(): string
    {
        return $this->codeName;
    }

    /**
     * Returns the full number as array
     * @return array
     */
    public function getInternalVersion(): array
    {
        return $this->internalVersion;
    }

    /**
     * Returns the full version number as formatted string
     * @return string
     */
    public function getInternalVersionAsString(): string
    {
        return implode('.', $this->internalVersion);
    }

    /**
     * Returns the Unix timestamp when the release was built
     * @return int
     */
    public function getBuildTimestamp(): int
    {
        return $this->buildDate;
    }

    /**
     * Returns the formatted date when the release was built.
     * @return string
     */
    public function getBuildDate(): string
    {
        return DateTimeHelper::timestampToFormattedDateTime($this->getBuildTimestamp());
    }
}
