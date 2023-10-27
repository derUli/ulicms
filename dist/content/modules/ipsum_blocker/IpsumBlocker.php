<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\HttpStatusCode;
use App\Controllers\MainClass;
use App\Utils\CacheUtil;

class IpsumBlocker extends MainClass {
    protected const MODULE_NAME = 'ipsum_blocker';

    protected const LIST_FETCH_URL = 'https://raw.githubusercontent.com/stamparm/ipsum/master/ipsum.txt';

    protected const LIST_EXPIRY = 60 * 60 * 24;

    /**
     * Do IP check before init
     *
     * @return void
     */
    public function beforeInit(): void {
        if(get_ip() && static::isBlocked(get_ip())) {
            HTMLResult(static::getBlockedMessage(get_ip()), HttpStatusCode::FORBIDDEN);
        }
    }

    /**
     * Get blocked message
     *
     * @param string $ip
     *
     * @return string
     *
     */
    public static function getBlockedMessage(string $ip): string {
        return "Access from your ip {$ip} was blocked because your ip is listed at <a href=\"https://github.com/stamparm/ipsum\">IPsum</a>." ;
    }

    /**
     * Fetch blocklist on cron
     *
     * @return void
     */
    public function cron(): void {
        $adapter = CacheUtil::getAdapter(true);

        if($adapter && ! $adapter->has('ipsum_blocklist')) {
            static::fetchIpsum();
        }
    }

    /**
     * Check if an IP is blocked
     *
     * @return bool
     */
    public static function isBlocked(string $ip): bool {
        $blockedIps = static::getBlockedIps();

        return in_array($ip, $blockedIps);
    }

    /**
     * Fetch ipsum blocklist
     *
     * @return string
     */
    public static function fetchIpsum(): ?string {
        $adapter = CacheUtil::getAdapter(true);

        if($adapter && $adapter->has('ipsum_blocklist')) {
            return $adapter->get('ipsum_blocklist');
        }

        $list = file_get_contents_wrapper(static::LIST_FETCH_URL, true);

        if($adapter) {
            $adapter->set('ipsum_blocklist', $list, static::LIST_EXPIRY);
        }

        return $list;
    }

    /**
     * Get blocked ips
     *
     * @return string[]
     */
    public static function getBlockedIps(): array {
        $list = (string)static::fetchIpsum();
        $lines = explode("\n", $list);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines,'strlen');

        $lines = array_filter($lines, static function($line) {
            return ! str_starts_with($line, '#');

        });

        $ips = [];

        foreach($lines as $line) {
            $lineSplit = explode("\t", $line);
            $ip = $lineSplit[0];

            $ips[] = $ip;
        }

        return $ips;
    }
}
