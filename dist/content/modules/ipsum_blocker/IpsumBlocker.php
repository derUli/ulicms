<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;
use App\Utils\CacheUtil;

class IpsumBlocker extends MainClass {
    public const LIST_FETCH_URL = 'https://raw.githubusercontent.com/stamparm/ipsum/master/ipsum.txt';

    public const LIST_EXPIRY = 60 * 60 * 24;

    public function beforeInit(): void {
        // $_SERVER['REMOTE_ADDR'] = '185.181.61.23';
        if(static::isBlocked(get_ip())) {
            // TODO:
            exit('Blocked by Ipsum');
        }
    }

    public function settings(): void{
        // TODO: IP List, "refresh" button and cron if better_cron is installed

    }

    public static function isBlocked(string $ip): bool {
        $blockedIps = static::getBlockedIps();

        return in_array($ip, $blockedIps);
    }

    // TODO: Run by better_cron
    public static function fetchIpsum(): ?string {
        $adapter = CacheUtil::getAdapter(true);

        if($adapter->has('ipsum_blocklist')) {
            return $adapter->get('ipsum_blocklist');
        }

        $list = file_get_contents_wrapper(static::LIST_FETCH_URL);

        $adapter->set('ipsum_blocklist', $list, static::LIST_EXPIRY);

        return $list;
    }

    public static function getBlockedIps(): array {
        $list = static::fetchIpsum();
        $lines = explode("\n", $list);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines,'strlen');

        $lines = array_filter($lines, static function($line) {
            return ! str_starts_with($line, '#');

        });

        $ips = [];

        foreach($lines as $line) {
            $lineSplit = explode("\t", $line);
            $ips[] = $lineSplit[0];
        }
        return $ips;
    }
}
