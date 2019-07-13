<?php

declare(strict_types=1);

namespace UliCMS\Security\SpamChecker;

use AntiSpamHelper;
use StringHelper;
use Settings;
use InvalidArgumentException;

class SpamFilterConfiguration {

    private $spamfilterEnabled = true;
    private $badwords = [];
    private $blockedCountries = [];
    private $disallowChineseChars = false;
    private $disallowCyrillicChars = false;
    private $disallowRtlChars = false;
    private $rejectRequestsFromBots = false;
    private $checkMxOfMailAddress = false;

    // TODO: Make class vars, getter and setter for all
    // spam filter related settings
    public static function fromSettings(): SpamFilterConfiguration {
        $settings = new SpamFilterConfiguration();
        $settings->setSpamFilterEnabled(AntiSpamHelper::isSpamFilterEnabled());
        $settings->setBadwords(Settings::get("spamfilter_words_blacklist"));
        $settings->setBlockedCountries(Settings::get("country_blacklist"));

        $settings->setDisallowChineseChars(Settings::get("disallow_chinese_chars"));
        $settings->setDisallowCyrillicChars(Settings::get("disallow_cyrillic_chars"));
        $settings->setDisallowRtlChars(Settings::get("disallow_rtl_chars"));

        $settings->setRejectRequestsFromBots(Settings::get("reject_requests_from_bots"));
        $settings->setCheckMxOfMailAddress(Settings::get("check_mx_of_mail_address"));

        // TODO: read other antispam settings

        return $settings;
    }

    public function getSpamFilterEnabled(): bool {
        return $this->spamfilterEnabled;
    }

    public function setSpamFilterEnabled(bool $val): void {
        $this->spamfilterEnabled = boolval($val);
    }

    public function getBadwords(): array {
        return $this->badwords;
    }

    public function setBadwords($val): void {
        if (is_string($val)) {
            $this->badwords = StringHelper::linesFromString($val);
        } else if (is_array($this->badwords)) {
            $this->badwords = $val;
        } else if (is_null($this->badwords)) {
            $this->badwords = [];
        } else {
            throw new InvalidArgumentException("$val is not a valid value for badwords");
        }
    }

    public function getBlockedCountries(): array {
        return $this->blockedCountries;
    }

    public function setBlockedCountries($val): void {
        if (is_string($val)) {
            $countries = explode(",", $val);
            $countries = array_map("trim", $countries);
            $countries = array_filter($countries);
            $countries = array_values($countries);
            $this->blockedCountries = $countries;
        } else if (is_array($val)) {
            $this->blockedCountries = $val;
        } else if (is_null($this->badwords)) {
            $this->badwords = [];
        } else {
            throw new InvalidArgumentException("$val is not a valid value for badwords");
        }
    }

    public function getDisallowChineseChars(): bool {
        return $this->disallowChineseChars;
    }

    public function setDisallowChineseChars(bool $val): void {
        $this->disallowChineseChars = boolval($val);
    }

    public function getDisallowCyrillicChars(): bool {
        return $this->disallowCyrillicChars;
    }

    public function setDisallowCyrillicChars(bool $val): void {
        $this->disallowCyrillicChars = boolval($val);
    }

    public function getDisallowRtlChars(): bool {
        return $this->disallowRtlChars;
    }

    public function setDisallowRtlChars(bool $val): void {
        $this->disallowRtlChars = boolval($val);
    }

    public function getRejectRequestsFromBots(): bool {
        return $this->rejectRequestsFromBots;
    }

    public function setRejectRequestsFromBots(bool $val): void {
        $this->rejectRequestsFromBots = boolval($val);
    }

    public function getCheckMxOfMailAddress(): bool {
        return $this->checkMxOfMailAddress;
    }

    public function setCheckMxOfMailAddress(bool $val): void {
        $this->checkMxOfMailAddress = boolval($val);
    }

}
