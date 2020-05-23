<?php

declare(strict_types=1);

class AntiSpamHelper extends Helper {

    // checking if this Country is blocked by spamfilter
    // blocking works by the domain extension of the client's
    // hostname
    public static function isCountryBlocked(?string $ip = null,
            ?array $country_blacklist = null): bool {
        if (is_null($ip)) {
            $ip = get_ip();
        }
        if (is_null($country_blacklist)) {
            $country_blacklist = Settings::get("country_blacklist");
        }
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

        for ($i = 0; $i < count($country_blacklist); $i++) {
            $ending = "." . $country_blacklist[$i];
            if (endsWith($hostname, $ending)) {
                return true;
            }
        }

        return false;
    }

    // returns true if a string contains chinese chars
    public static function isChinese(?string $str): bool {
        if (!$str) {
            return false;
        }

        return (bool) preg_match("/\p{Han}+/u", $str);
    }

    // returns true if a string contains cyrillic chars
    public static function isCyrillic(?string $str): bool {
        if (!$str) {
            return false;
        }

        return (bool) preg_match('/\p{Cyrillic}+/u', $str);
    }

    // returns true if a string contains chars in
    // right to left languages such as arabic
    public static function isRtl(?string $str): bool {
        if (!$str) {
            return false;
        }

        $rtl_chars_pattern = '/[\x{0590}-\x{05ff}\x{0600}-\x{06ff}]/u';
        return (bool) preg_match($rtl_chars_pattern, $str);
    }

    // returns the first matching word if the string contains badwords
    // badwords can be specified at the spamfilter settings
    // returns null if there are no badwords
    public static function containsBadwords(
            ?string $str,
            array $words_blacklist = null
    ) {
        if (!$str) {
            return null;
        }
        if (is_null($words_blacklist)) {
            $words_blacklist = Settings::get("spamfilter_words_blacklist");
        }
        if (is_string($words_blacklist)) {
            $words_blacklist = StringHelper::linesFromString(
                            $words_blacklist,
                            false,
                            true,
                            true
            );
        }
        for ($i = 0; $i < count($words_blacklist); $i++) {
            $word = strtolower($words_blacklist[$i]);
            if (strpos(strtolower($str), $word) !== false) {
                return $words_blacklist[$i];
            }
        }

        return null;
    }

    // returns true if the spamfilter is enabled
    public static function isSpamFilterEnabled(): bool {
        return Settings::get("spamfilter_enabled") == "yes";
    }

    // returns true if this is a bot, based on a static useragent list
    public static function checkForBot(?string $useragent = null): bool {
        if (!$useragent and isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (!$useragent) {
            return false;
        }
        $bots = [
            "Indy",
            "Blaiz",
            "Java",
            "libwww-perl",
            "Python",
            "OutfoxBot",
            "User-Agent",
            "PycURL",
            "AlphaServer",
            "T8Abot",
            "Syntryx",
            "WinHttp",
            "WebBandit",
            "nicebot",
            "Teoma",
            "alexa",
            "froogle",
            "inktomi",
            "looksmart",
            "URL_Spider_SQL",
            "Firefly",
            "NationalDirectory",
            "Ask Jeeves",
            "TECNOSEEK",
            "InfoSeek",
            "WebFindBot",
            "girafabot",
            "crawler",
            "www.galaxy.com",
            "Googlebot",
            "Scooter",
            "Slurp",
            "appie",
            "FAST",
            "WebBug",
            "Spade",
            "ZyBorg",
            "rabaz"
        ];
        foreach ($bots as $bot) {
            if ($useragent && stripos($useragent, $bot) !== false) {
                return true;
            }
        }
        return false;
    }

    // This function checks if the domain of an email address has a mx nds entry
    // if it is invalid there is a high chance, that this is not valid
    // email address
    // please note that this function returns also true if
    // you send an email to a nonexisting user on a valid domain.
    // Use this function with care
    public static function checkMailDomain(string $email): bool {
        $domain = strstr($email, '@');
        $domain = remove_prefix($domain, "@");
        // In some cases getmxrr() would return a result for an invalid domain
        // if there is no additional dot at the end
        $domain = !endsWith($domain, ".") ? $domain . "." : $domain;
        $result = [];

        // sometimes getmxrr returns true even if the result is empty
        // so check the count of $result
        getmxrr($domain, $result);
        return count($result) > 0;
    }

}
