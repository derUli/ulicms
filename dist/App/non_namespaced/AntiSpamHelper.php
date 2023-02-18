<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');


/**
 * Class with methods for checking comments for spam
 */
class AntiSpamHelper extends Helper
{
    // checking if this Country is blocked by spamfilter
    // blocking works by the domain extension of the client's
    // hostname
    public static function isCountryBlocked(
        ?string $ip = null,
        ?array $country_blacklist = null
    ): bool {
        $ip = $ip ?? get_ip();

        $country_blacklist = $country_blacklist ?? Settings::get("country_blacklist");

        if (is_string($country_blacklist)) {
            $country_blacklist = strtolower($country_blacklist);
            $country_blacklist = explode(",", $country_blacklist);
            $country_blacklist = array_map("trim", $country_blacklist);
            $country_blacklist = array_filter($country_blacklist);
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        @$hostname = gethostbyaddr($ip);

        if (!$hostname || $hostname === $ip) {
            return false;
        }

        $hostname = strtolower($hostname);

        $blacklistCount = count($country_blacklist);

        for ($i = 0; $i < $blacklistCount; $i++) {
            $ending = '.' . $country_blacklist[$i];
            if (str_ends_with($hostname, $ending)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string contains chinese chars
     * @param string|null $str
     * @return bool
     */
    public static function isChinese(?string $str): bool
    {
        if (!$str) {
            return false;
        }

        return (bool) preg_match("/\p{Han}+/u", $str);
    }

    /**
     * Check if a string contains cyrillic chars
     * @param string|null $str
     * @return bool
     */
    public static function isCyrillic(?string $str): bool
    {
        if (!$str) {
            return false;
        }

        return (bool) preg_match('/\p{Cyrillic}+/u', $str);
    }

    /**
     * Check if a string contains text in right to left languages
     * @param string|null $str
     * @return bool
     */
    public static function isRtl(?string $str): bool
    {
        if (!$str) {
            return false;
        }

        $rtl_chars_pattern = '/[\x{0590}-\x{05ff}\x{0600}-\x{06ff}]/u';
        return (bool) preg_match($rtl_chars_pattern, $str);
    }

    /**
     * Check if the string contains forbidden words
     * @param string|null $str
     * @param array $words_blacklist
     * @return type
     */
    public static function containsBadwords(
        ?string $str,
        array $words_blacklist = null
    ) {
        if (!$str) {
            return null;
        }

        $words_blacklist = $words_blacklist ?? Settings::get("spamfilter_words_blacklist");

        if (is_string($words_blacklist)) {
            $words_blacklist = StringHelper::linesFromString(
                $words_blacklist,
                false,
                true,
                true
            );
        }

        $wordCount = count($words_blacklist);
        for ($i = 0; $i < $wordCount; $i++) {
            $word = strtolower($words_blacklist[$i]);
            if (strpos(strtolower($str), $word) !== false) {
                return $words_blacklist[$i];
            }
        }

        return null;
    }

    /**
     * Check if the spam filter is enabled
     * @return bool
     */
    public static function isSpamFilterEnabled(): bool
    {
        return Settings::get("spamfilter_enabled") == "yes";
    }

    /**
     * Check if the client is a bot / crawler based on it's user agent
     * @param string|null $useragent
     * @return bool
     */
    public static function checkForBot(?string $useragent = null): bool
    {
        $useragent = $useragent ?? Request::getUserAgent();

        return is_crawler($useragent);
    }

    // This function checks if the domain of an email address has a mx nds entry
    // if it is invalid there is a high chance, that this is not valid
    // email address
    // please note that this function returns also true if
    // you send an email to a nonexisting user on a valid domain.
    // Use this function with care
    public static function checkMailDomain(string $email): bool
    {
        $domain = strstr($email, '@');
        $domain = remove_prefix($domain, "@");
        // In some cases getmxrr() would return a result for an invalid domain
        // if there is no additional dot at the end
        $domain = !str_ends_with($domain, '.') ? $domain . '.' : $domain;
        $result = [];

        // sometimes getmxrr returns true even if the result is empty
        // so check the count of $result
        getmxrr($domain, $result);
        return count($result) > 0;
    }
}
