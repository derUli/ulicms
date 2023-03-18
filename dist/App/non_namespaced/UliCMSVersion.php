<?php

declare(strict_types=1);

use App\Helpers\DateTimeHelper;

/**
 * Provides information about the UliCMS release version
 */
class UliCMSVersion
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->codename = "Beetle-Eating Nandu";
        $this->releaseYear = 2023;
        $this->buildDate = 0; // {InsertBuildDate}
        $this->internalVersion = [
            2023,
            2
        ];

        $this->update = '';
    }

    /**
     * Returns the release year
     * @return string
     */
    public function getReleaseYear(): string
    {
        return (string) $this->releaseYear;
    }

    /**
     * Returns the release Codename
     * @return string
     */
    public function getCodeName(): string
    {
        return $this->codename;
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
        $timezone = DateTimeHelper::getCurrentTimezone();
        $currentLocale = DateTimeHelper::getCurrentLocale();

        $formatter = new IntlDateFormatter($currentLocale, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM, $timezone);
        $pattern = str_replace(',', '', $formatter->getPattern());
        $formatter->setPattern($pattern);

        return $formatter->format($this->getBuildTimestamp());
    }
}
