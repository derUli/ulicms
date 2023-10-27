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

        $this->assertGreaterThanOrEqual(1000000, strlen($fetch1));
    }

    public function testCron(): void {
        $adapter = CacheUtil::getAdapter(true);

        $this->assertFalse($adapter->has('ipsum_blocklist'));
        $controller = new IpsumBlocker();

        $controller->cron();
        // $controller->cron();

        $this->assertTrue($adapter->has('ipsum_blocklist'));

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

    public function testGetBlockedMessage(): void {
        $this->assertStringContainsString(
            'Access from your ip 38.97.116.244 was blocked because your ip is listed at',
            IpsumBlocker::getBlockedMessage('38.97.116.244')
        );
    }
}
