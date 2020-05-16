<?php

namespace UliCMS\Backend\Utils;

class BrowserCompatiblityChecker {
    private $unsupportedBrowserName = null;
    private $useragent = '';

    public function __construct(string $useragent) {
        $this->useragent = $useragent;
    }

    public function isCompatible(): bool {
        if ($this->isIE()) {
            $this->unsupportedBrowserName = "Microsoft Internet Explorer";
        }

        return empty($this->unsupportedBrowserName);
    }
    
    public function getUnsupportedBrowserName(): ?string {
        return $this->unsupportedBrowserName;
    }

    public function isIE(): bool {
        return (preg_match('~MSIE|Internet Explorer~i', $this->useragent) ||
                (strpos($this->useragent, 'Trident/7.0') !== false));
    }

}
