<?php

use App\Models\Content\Comment;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

if (!defined("LOADED_LANGUAGE_FILE")) {
    setLanguageByDomain();

    $languages = getAllLanguages();

    if (!is_array($_SESSION)) {
        $_SESSION = [];
    }

    if (!empty($_GET['language']) && in_array($_GET['language'], $languages)) {
        $_SESSION['language'] = Database::escapeValue(
            $_GET['language'],
            DB_TYPE_STRING
        );
    }

    if (!isset($_SESSION['language'])) {
        $_SESSION['language'] = Settings::get("default_language");
    }

    setLocaleByLanguage();

    if (in_array($_SESSION['language'], $languages) &&
            file_exists(getLanguageFilePath($_SESSION['language']))) {
        require_once getLanguageFilePath($_SESSION['language']);
    } elseif (file_exists(getLanguageFilePath('en'))) {
        require getLanguageFilePath('en');
    }

    Translation::loadAllModuleLanguageFiles($_SESSION['language']);
    Translation::includeCustomLangFile($_SESSION['language']);
}

if (Settings::get("delete_ips_after_48_hours")) {
    $keep_spam_ips = Settings::get("keep_spam_ips");
    Comment::deleteIpsAfter48Hours($keep_spam_ips);
}

$empty_trash_days = Settings::get("empty_trash_days");

if ($empty_trash_days === false) {
    $empty_trash_days = 30;
}

// Papierkorb für Seiten Cronjob
$empty_trash_timestamp = $empty_trash_days * (60 * 60 * 24);
Database::query("DELETE FROM " . tbname("content") . " WHERE " . time() .
                " -  `deleted_at` > $empty_trash_timestamp") || die(Database::getLastError());

// Cronjobs der Module
do_event("cron");
