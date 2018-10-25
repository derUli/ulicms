<?php

class AntiSpamHelper
{

    // checking if this Country is blocked by spamfilter
    public static function isCountryBlocked()
    {
        $country_blacklist = Settings::get("country_blacklist");
        $country_whitelist = Settings::get("country_whitelist");
        
        $country_blacklist = str_replace(" ", "", $country_blacklist);
        $country_whitelist = str_replace(" ", "", $country_whitelist);
        
        $country_blacklist = strtolower($country_blacklist);
        $country_whitelist = strtolower($country_whitelist);
        
        $country_blacklist = explode(",", $country_blacklist);
        $country_whitelist = explode(",", $country_whitelist);
        
        $ip_adress = $_SERVER["REMOTE_ADDR"];
        
        @$hostname = gethostbyaddr($ip_adress);
        
        if (! $hostname) {
            return false;
        }
        
        $hostname = strtolower($hostname);
        
        for ($i = 0; $i < count($country_whitelist); $i ++) {
            $ending = "." . $country_whitelist[$i];
            if (EndsWith($hostname, $ending)) {
                return false;
            }
        }
        
        for ($i = 0; $i < count($country_blacklist); $i ++) {
            $ending = "." . $country_blacklist[$i];
            if (EndsWith($hostname, $ending)) {
                return true;
            }
        }
        
        return false;
    }

    public static function isChinese($str)
    {
        return (bool) preg_match("/\p{Han}+/u", $str);
    }

    public static function isCyrillic($str)
    {
        return (bool) preg_match('/\p{Cyrillic}+/u', $str);
    }

    public static function containsBadwords($str)
    {
        $words_blacklist = Settings::get("spamfilter_words_blacklist");
        $str = strtolower($str);
        
        if ($words_blacklist) {
            $words_blacklist = StringHelper::linesFromString($words_blacklist, false, true, true);
        } else {
            return null;
        }
        
        for ($i = 0; $i < count($words_blacklist); $i ++) {
            $word = strtolower($words_blacklist[$i]);
            if (strpos(strtolower($str), $word) !== false) {
                return $words_blacklist[$i];
            }
        }
        
        return null;
    }

    public static function isSpamFilterEnabled()
    {
        return Settings::get("spamfilter_enabled") == "yes";
    }

    public static function checkForBot($useragent = null)
    {
        if (! $useragent) {
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

    public static function checkMailDomain($email)
    {
        $domain = strstr($email, '@');
        $domain = remove_prefix($domain, "@");
        $domain = ! endsWith($domain, ".") ? $domain . "." : $domain;
        $result = array();
        getmxrr($domain, $result);
        return count($result) > 0;
    }
}
