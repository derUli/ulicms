<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;

class CoreContentController extends MainClass {
    public function cron(): void {
        // Hard delete deleted content after X days (0 means disable trash bin)
        $empty_trash_days = (int)Settings::get('empty_trash_days');
        $empty_trash_timestamp = $empty_trash_days * (60 * 60 * 24);
        Database::query('DELETE FROM ' . Database::tableName('content') . ' WHERE ' . time() .
                " -  `deleted_at` > {$empty_trash_timestamp}");
    }
}
