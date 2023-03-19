<?php

declare(strict_types=1);

namespace App\Security\SpamChecker;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use App\Helpers\AntiSpamHelper;
use Settings;
use InvalidArgumentException;

class SpamFilterConfiguration
{
    private $spamfilterEnabled = true;
    private $badwords = [];
    private $blockedCountries = [];
    private $disallowChineseChars = false;
    private $disallowCyrillicChars = false;
    private $disallowRtlChars = false;
    private $rejectRequestsFromBots = false;
    private $checkMxOfMailAddress = false;

    // load configuration from settings
    public static function fromSettings(): SpamFilterConfiguration
    {
        $settings = new SpamFilterConfiguration();
        $settings->setSpamFilterEnabled(AntiSpamHelper::isSpamFilterEnabled());
        $settings->setBadwords(
            Settings::get("spamfilter_words_blacklist")
        );
        $settings->setBlockedCountries(Settings::get("country_blacklist"));

        $settings->setDisallowChineseChars(
            boolval(Settings::get("disallow_chinese_chars"))
        );

        $disallow_cyrillic_chars = boolval(
            Settings::get("disallow_cyrillic_chars")
        );
        $settings->setDisallowCyrillicChars($disallow_cyrillic_chars);

        $disallow_rtl_chars = boolval(
            Settings::get("disallow_rtl_chars")
        );

        $settings->setDisallowRtlChars($disallow_rtl_chars);
        $settings->setRejectRequestsFromBots(
            boolval(Settings::get("reject_requests_from_bots"))
        );

        $checkMx = boolval(Settings::get("check_mx_of_mail_address"));
        $settings->setCheckMxOfMailAddress($checkMx);

        return $settings;
    }

    public function getSpamFilterEnabled(): bool
    {
        return $this->spamfilterEnabled;
    }

    public function setSpamFilterEnabled(bool $val): void
    {
        $this->spamfilterEnabled = (bool)$val;
    }

    public function getBadwords(): array
    {
        return $this->badwords;
    }

    public function setBadwords($val): void
    {
        if (is_string($val)) {
            $this->badwords = \App\Helpers\StringHelper::linesFromString($val);
        } elseif (is_array($val)) {
            $this->badwords = $val;
        } elseif ($val === null) {
            $this->badwords = [];
        } else {
            throw new InvalidArgumentException(var_dump_str($val) .
                    " is not a valid value for badwords");
        }
    }

    public function getBlockedCountries(): array
    {
        return $this->blockedCountries;
    }

    public function setBlockedCountries($val): void
    {
        if (is_string($val)) {
            $countries = explode(",", $val);
            $countries = array_map("trim", $countries);
            $countries = array_filter($countries);
            $countries = array_values($countries);
            $this->blockedCountries = $countries;
        } elseif (is_array($val)) {
            $this->blockedCountries = $val;
        } elseif ($val === null) {
            $this->blockedCountries = [];
        } else {
            throw new InvalidArgumentException(var_dump_str($val) .
                    " is not a valid value for badwords");
        }
    }

    public function getDisallowChineseChars(): bool
    {
        return $this->disallowChineseChars;
    }

    public function setDisallowChineseChars(bool $val): void
    {
        $this->disallowChineseChars = (bool)$val;
    }

    public function getDisallowCyrillicChars(): bool
    {
        return $this->disallowCyrillicChars;
    }

    public function setDisallowCyrillicChars(bool $val): void
    {
        $this->disallowCyrillicChars = (bool)$val;
    }

    public function getDisallowRtlChars(): bool
    {
        return $this->disallowRtlChars;
    }

    public function setDisallowRtlChars(bool $val): void
    {
        $this->disallowRtlChars = (bool)$val;
    }

    public function getRejectRequestsFromBots(): bool
    {
        return $this->rejectRequestsFromBots;
    }

    public function setRejectRequestsFromBots(bool $val): void
    {
        $this->rejectRequestsFromBots = (bool)$val;
    }

    public function getCheckMxOfMailAddress(): bool
    {
        return $this->checkMxOfMailAddress;
    }

    public function setCheckMxOfMailAddress(bool $val): void
    {
        $this->checkMxOfMailAddress = (bool)$val;
    }
}
