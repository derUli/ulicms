<?php

declare(strict_types=1);

namespace App\Packages;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use function getThemeMeta;
use function getTemplateDirPath;

/**
 * This class represents a installed theme
 */
class Theme
{
    private $name = null;

    /**
     * Constructor
     * @param type $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get version number
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return getThemeMeta($this->name, "version");
    }

    /**
     * Get the path of the screenshot file
     * @return string|null
     */
    public function getScreenshotFile(): ?string
    {
        $screenshotFile = null;
        $screenshotFiles = [
            "screenshot.jpg",
            "screenshot.png",
            "screenshot.gif"
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
