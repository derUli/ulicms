<?php

use App\Models\Content\Comment;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

// Delete ip addresses of comments after 48 hours to be GDPR compliant
if (Settings::get('delete_ips_after_48_hours')) {
    // Optional keep stored ip addresses of spam comments
    $keep_spam_ips = (bool)Settings::get('keep_spam_ips');

    Comment::deleteIpsAfter48Hours($keep_spam_ips);
}

// Hard delete deleted content after X days (0 means disable trash bin)
$empty_trash_days = (int)Settings::get('empty_trash_days');
$empty_trash_timestamp = $empty_trash_days * (60 * 60 * 24);
Database::query('DELETE FROM ' . Database::tableName('content') . ' WHERE ' . time() .
                " -  `deleted_at` > {$empty_trash_timestamp}");

// Run cron tasks of modules
do_event('cron');
