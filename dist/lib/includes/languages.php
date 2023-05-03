<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

/**
 * Get all languages that have content
 *
 * @return string[]
 */
function getAllUsedLanguages(): array {
    $languages = [];
    $sql = 'select language from `{prefix}content` where active = 1 '
            . 'group by language order by language';
    $result = Database::query($sql, true);
    while ($row = Database::fetchobject($result)) {
        $languages[] = $row->language;
    }
    return $languages;
}

/**
 * Get path to a UliCMS core language file
 *
 * @param string $lang
 *
 * @return string
 */
function getLanguageFilePath(string $lang = 'de'): string {
    return ULICMS_ROOT . '/lang/' . $lang . '.php';
}

/**
 * Get available backend languages
 *
 * @return string[]
 */
function getAvailableBackendLanguages(): array {
    $langdir = ULICMS_ROOT . '/lang/';
    $list = scandir($langdir);
    sort($list);
    $retval = [];
    $listCount = count($list);
    for ($i = 0; $i < $listCount; $i++) {
        if (str_ends_with($list[$i], '.php')) {
            $retval[] = basename($list[$i], '.php');
        }
    }

    return $retval;
}

function getSystemLanguage(): string {
    if (isset($_SESSION['system_language'])) {
        $lang = $_SESSION['system_language'];
    } elseif (isset($_SESSION['language'])) {
        $lang = $_SESSION['language'];
    } elseif (Settings::get('system_language')) {
        $lang = Settings::get('system_language');
    } else {
        $lang = 'de';
    }
    if (! is_file(getLanguageFilePath($lang))) {
        $lang = 'de';
    }
    return $lang;
}

function getDomainByLanguage($givenLanguage): ?string {
    $domainMapping = Settings::get('domain_to_language');
    $domainMapping = Settings::mappingStringToArray($domainMapping);
    foreach ($domainMapping as $domain => $language) {
        if ($givenLanguage == $language) {
            return $domain;
        }
    }
    return null;
}

/**
 * Set language by domain (Domain2Language Mapping)
 *
 * @return bool
 */
function setLanguageByDomain(): bool {
    $domainMapping = Settings::get('domain_to_language');
    $domainMapping = Settings::mappingStringToArray($domainMapping);

    foreach ($domainMapping as $domain => $language) {
        $givenDomain = $_SERVER['HTTP_HOST'];
        if ($domain == $givenDomain && in_array(
            $language,
            getAllLanguages()
        )
        ) {
            $_SESSION['language'] = $language;
            return true;
        }
    }

    return false;
}

function getLanguageNameByCode(string $code): string {
    $result = Database::query(
        'SELECT name FROM `' . Database::tableName('languages') .
        "` WHERE language_code = '" . Database::escapeValue($code) . "'"
    );
    $retval = $code;
    if (Database::getNumRows($result) > 0) {
        $dataset = Database::fetchObject($result);
        $retval = $dataset->name;
    }

    return $retval;
}

// Returns the language code of the current language
// If $current is true returns language of the current page
// else it returns $_SESSION['language'];
function getCurrentLanguage($current = false): string {
    if (\App\Storages\Vars::get('current_language_' . strbool($current))) {
        return \App\Storages\Vars::get('current_language_' . strbool($current));
    }

    if ($current) {
        $result = Database::query('SELECT language FROM ' . Database::tableName('content') .
                " WHERE slug='" . get_slug() . "'");
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $language = $dataset->language;
            \App\Storages\Vars::set('current_language_' . strbool($current), $language);
        }
    }

    if (isset($_SESSION['language'])) {
        return basename($_SESSION['language']);
    }

    return basename(Settings::get('default_language'));
}

/**
 * Get current frontend language or default langauge
 *
 * @return string
 */
function getFrontendLanguage() {
    $domainLanguage = get_domain() ?
            getDomainByLanguage(get_domain()) : null;
    $fallbackLanguage = $domainLanguage ?: Settings::get('language');

    return $_SESSION['language'] ?? $fallbackLanguage;
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

    if (\App\Storages\Vars::get('all_languages') !== null) {
        return \App\Storages\Vars::get('all_languages');
    }
    $result = Database::query('SELECT language_code FROM `' . Database::tableName('languages') .
            '` ORDER BY language_code');

    while ($row = Database::fetchObject($result)) {
        $languageCodes[] = $row->language_code;
    }
    \App\Storages\Vars::set('all_languages', $languageCodes);
    return $languageCodes;
}
