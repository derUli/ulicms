<?php

declare(strict_types=1);

use App\Security\PermissionChecker;
use Negotiation\LanguageNegotiator;

function getAllUsedLanguages(): array {
    $languages = [];
    $sql = "select language from `{prefix}content` where active = 1 "
            . "group by language order by language";
    $result = Database::query($sql, true);
    while ($row = Database::fetchobject($result)) {
        $languages[] = $row->language;
    }
    return $languages;
}

function get_prefered_language(
        array $priorities,
        ?string $http_accept_language
) {
    $negotiator = new LanguageNegotiator();
    return $negotiator->getBest($http_accept_language, $priorities)->getType();
}

function getLanguageFilePath(string $lang = "de"): string {
    return ULICMS_ROOT . "/lang/" . $lang . ".php";
}

function getAvailableBackendLanguages(): array {
    $langdir = ULICMS_ROOT . "/lang/";
    $list = scandir($langdir);
    sort($list);
    $retval = [];
    $listCount = count($list);
    for ($i = 0; $i < $listCount; $i++) {
        if (endsWith($list[$i], ".php")) {
            $retval[] = basename($list[$i], ".php");
        }
    }

    return $retval;
}

function getSystemLanguage(): string {
    if (isset($_SESSION["system_language"])) {
        $lang = $_SESSION["system_language"];
    } elseif (isset($_SESSION["language"])) {
        $lang = $_SESSION["language"];
    } elseif (Settings::get("system_language")) {
        $lang = Settings::get("system_language");
    } else {
        $lang = "de";
    }
    if (!is_file(getLanguageFilePath($lang))) {
        $lang = "de";
    }
    return $lang;
}

function getDomainByLanguage($givenLanguage): ?string {
    $domainMapping = Settings::get("domain_to_language");
    $domainMapping = Settings::mappingStringToArray($domainMapping);
    foreach ($domainMapping as $domain => $language) {
        if ($givenLanguage == $language) {
            return $domain;
        }
    }
    return null;
}

function getLanguageByDomain($givenDomain): ?string {
    $domainMapping = Settings::get("domain_to_language");
    $domainMapping = Settings::mappingStringToArray($domainMapping);
    foreach ($domainMapping as $domain => $language) {
        if ($givenDomain == $domain) {
            return $language;
        }
    }
    return null;
}

function setLanguageByDomain(): bool {
    $domainMapping = Settings::get("domain_to_language");
    $domainMapping = Settings::mappingStringToArray($domainMapping);

    foreach ($domainMapping as $domain => $language) {
        $givenDomain = $_SERVER["HTTP_HOST"];
        if ($domain == $givenDomain && in_array(
                        $language,
                        getAllLanguages()
                )
        ) {
            $_SESSION["language"] = $language;
            return true;
        }
    }

    return false;
}

function getLanguageNameByCode(string $code): string {
    $result = db_query(
            "SELECT name FROM `" . tbname("languages") .
            "` WHERE language_code = '" . db_escape($code) . "'"
    );
    $retval = $code;
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        $retval = $dataset->name;
    }

    return $retval;
}

function setLocaleByLanguage(): array {
    $locale = [];

    $var = (is_admin_dir() && isset($_SESSION["system_language"])) ?
            "locale_" . $_SESSION["system_language"] :
            "locale_" . getFrontendLanguage();

    $localeSetting = Settings::get($var) ?
            Settings::get($var) : Settings::get("locale");

    if ($localeSetting) {
        $locale = splitAndTrim($localeSetting);
        array_unshift($locale, LC_ALL);
    }

    @call_user_func_array("setlocale", $locale);
    return $locale;
}

// Returns the language code of the current language
// If $current is true returns language of the current page
// else it returns $_SESSION["language"];
function getCurrentLanguage($current = false): string {
    if (Vars::get("current_language_" . strbool($current))) {
        return Vars::get("current_language_" . strbool($current));
    }
    if ($current) {
        $result = db_query("SELECT language FROM " . tbname("content") .
                " WHERE slug='" . get_slug() . "'");
        if (db_num_rows($result) > 0) {
            $dataset = db_fetch_object($result);
            $language = $dataset->language;
            Vars::set("current_language_" . strbool($current), $language);
        }
    }

    if (isset($_SESSION["language"])) {
        return basename($_SESSION["language"]);
    } else {
        return basename(Settings::get("default_language"));
    }
}

// Sprachcodes abfragen und als Array zurück geben
function getAllLanguages($filtered = false): array {
    $languageCodes = [];

    if ($filtered) {
        $permissionChecker = new PermissionChecker(get_user_id());
        $languages = $permissionChecker->getLanguages();
        if (count($languages) > 0) {
            $result = [];
            foreach ($languages as $lang) {
                $result[] = $lang->getLanguageCode();
            }
            return $result;
        }
    }

    if (Vars::get("all_languages") !== NULL) {
        return Vars::get("all_languages");
    }
    $result = db_query("SELECT language_code FROM `" . tbname("languages") .
            "` ORDER BY language_code");

    while ($row = db_fetch_object($result)) {
        array_push($languageCodes, $row->language_code);
    }
    Vars::set("all_languages", $languageCodes);
    return $languageCodes;
}
