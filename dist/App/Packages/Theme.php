<?php

declare(strict_types=1);

namespace App\Packages;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use function getTemplateDirPath;
use function getThemeMeta;

/**
 * This class represents a installed theme
 */
class Theme
{
    private string $name;

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get version number
     *
     * @return string
     */
    public function getVersion(): ?string
    {
        return getThemeMeta($this->name, 'version') ? getThemeMeta($this->name, 'version') : null;
    }

    /**
     * Get the path of the screenshot file
     *
     * @return string|null
     */
    public function getScreenshotFile(): ?string
    {
        $screenshotFile = null;
        $screenshotFiles = [
            'screenshot.jpg',
            'screenshot.png',
            'screenshot.gif'
        ];

        foreach ($screenshotFiles as $file) {
            $fullPath = getTemplateDirPath($this->name) . $file;
            if (is_file($fullPath)) {
                $screenshotFile = $fullPath;
            }
        }

        return $screenshotFile;
    }

    /**
     * Check if this theme has a screeenshot
     * @return bool
     */
    public function hasScreenshot(): bool
    {
        return $this->getScreenshotFile() !== null;
    }
}
