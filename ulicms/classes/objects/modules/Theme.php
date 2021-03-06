<?php

declare(strict_types=1);

namespace UliCMS\Packages;

use function getThemeMeta;
use function getTemplateDirPath;

class Theme
{
    private $name = null;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getVersion(): ?string
    {
        return getThemeMeta($this->name, "version");
    }

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
            if (file_exists($fullPath)) {
                $screenshotFile = $fullPath;
            }
        }
        return $screenshotFile;
    }

    public function hasScreenshot(): bool
    {
        return !is_null($this->getScreenshotFile());
    }
}
