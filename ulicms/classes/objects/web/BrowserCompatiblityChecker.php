<?php

namespace UliCMS\Backend\Utils;

class BrowserCompatiblityChecker {

    private $unsupportedBrowserName = null;
    private $useragent = '';

    public function __construct(string $useragent) {
        $this->useragent = $useragent;
    }

    // All current browsers except Internet Explorer, Opera Mini
    // and Text Mode browsers are supported
    public function isCompatible(): bool {
        if ($this->isIE()) {
            $this->unsupportedBrowserName = "Microsoft Internet Explorer";
        } else if ($this->isOperaMini()) {
            $this->unsupportedBrowserName = "Opera Mini";
        } else if ($this->isLynx()) {
            $this->unsupportedBrowserName = "Lynx";
        } else if ($this->isELinks()) {
            $this->unsupportedBrowserName = "ELinks";
        } else if ($this->isLinks()) {
            $this->unsupportedBrowserName = "Links";
        } else if ($this->isW3M()) {
            $this->unsupportedBrowserName = "w3m";
        } else if ($this->isDillo()) {
            $this->unsupportedBrowserName = "Dillo";
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

    public function isLynx(): bool {
        return strpos($this->useragent, 'Lynx/') !== false;
    }

    public function isELinks(): bool {
        return strpos($this->useragent, 'ELinks/') !== false;
    }

    public function isOperaMini(): bool {
        return strpos($this->useragent, 'Opera Mini') !== false;
    }

    public function isLinks(): bool {
        return strpos($this->useragent, 'Links (2') !== false ||
                strpos($this->useragent, 'Links (1') !== false;
    }

    public function isW3M(): bool {
        return strpos($this->useragent, 'w3m/') !== false;
    }

    public function isDillo(): bool {
        return strpos($this->useragent, 'Dillo') !== false;
    }

}
