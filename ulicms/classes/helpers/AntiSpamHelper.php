<?php

class AntiSpamHelper extends Helper {

    // checking if this Country is blocked by spamfilter
    public static function isCountryBlocked($ip = null, $country_blacklist = null) {
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

        @$hostname = gethostbyaddr($ip);

        if (!$hostname) {
            return false;
        }

        $hostname = strtolower($hostname);

        for ($i = 0; $i < count($country_blacklist); $i ++) {
            $ending = "." . $country_blacklist[$i];
            if (EndsWith($hostname, $ending)) {
                return true;
            }
        }

        return false;
    }

    public static function isChinese($str) {
        return (bool) preg_match("/\p{Han}+/u", $str);
    }

    public static function isCyrillic($str) {
        return (bool) preg_match('/\p{Cyrillic}+/u', $str);
    }

    public static function isRtl($str) {
        $rtl_chars_pattern = '/[\x{0590}-\x{05ff}\x{0600}-\x{06ff}]/u';
        return (bool) preg_match($rtl_chars_pattern, $str);
    }

    public static function containsBadwords($str, $words_blacklist = null) {
        if (is_null($words_blacklist)) {
            $words_blacklist = Settings::get("spamfilter_words_blacklist");
        }
        if (is_string($words_blacklist)) {
            $words_blacklist = StringHelper::linesFromString($words_blacklist, false, true, true);
        }
        for ($i = 0; $i < count($words_blacklist); $i ++) {
            $word = strtolower($words_blacklist[$i]);
            if (strpos(strtolower($str), $word) !== false) {
                return $words_blacklist[$i];
            }
        }

        return null;
    }

    public static function isSpamFilterEnabled() {
        return Settings::get("spamfilter_enabled") == "yes";
    }

    public static function checkForBot($useragent = null) {
        if (!$useragent) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
        }
        $bots = array(
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
        );
        foreach ($bots as $bot) {
            if (stripos($useragent, $bot) !== false) {
                return true;
            }
            if (empty($useragent) || $useragent == " ") {
                return true;
            }
        }
        return false;
    }

    // This function checks if the domain of an email address has a mx nds entry
    // if it is invalid there is a high chance, that this is not valid email address
    // please note that this function returns also true if
    // you send an email to a nonexisting user on a valid domain.
    // Use this function with care
    public static function checkMailDomain($email) {
        $domain = strstr($email, '@');
        $domain = remove_prefix($domain, "@");
        // In some cases getmxrr() would return a result for an invalid domain if there is no additional dot at the end
        $domain = !endsWith($domain, ".") ? $domain . "." : $domain;
        $result = [];

        // sometimes getmxrr returns true even if the result is empty
        // so check the count of $result
        getmxrr($domain, $result);
        return count($result) > 0;
    }

}
