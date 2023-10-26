<?php

use App\Utils\CacheUtil;
use PHPUnit\Framework\TestCase;

class IpsumBlockerTest extends TestCase {
    public function tearDown(): void {
        CacheUtil::getAdapter(true)->delete('ipsum_blocklist');
    }

    public function testIsBlockedReturnsTrue(): void {
        $this->assertTrue(IpsumBlocker::isBlocked('185.181.61.23'));
    }

    public function testIsBlockedReturnsFalse(): void {
        $this->assertFalse(IpsumBlocker::isBlocked('8.8.8.8'));
    }

    public function testFetchIpsum(): void {
        $fetch1 = IpsumBlocker::fetchIpsum();
        $fetch2 = IpsumBlocker::fetchIpsum();

        $this->assertEquals($fetch2, $fetch1);
    }

    /**
     * @large
     */
    public function testGetBlockedIps(): void {
        $blockedIps = IpsumBlocker::getBlockedIps();

        $this->assertGreaterThanOrEqual(25500, $blockedIps);

        foreach($blockedIps as $ip) {
            $this->assertEquals($ip, filter_var($ip, FILTER_VALIDATE_IP));
        }
    }
}
