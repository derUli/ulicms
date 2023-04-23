<?php

use App\Security\SpamChecker\SpamFilterConfiguration;

class SpamFilterConfigurationTest extends \PHPUnit\Framework\TestCase {
    public function testSetSpamfilterEnabled(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertTrue($configuration->getSpamFilterEnabled());

        $configuration->setSpamFilterEnabled(false);
        $this->assertFalse($configuration->getSpamFilterEnabled());
        $configuration->setSpamFilterEnabled(true);
        $this->assertTrue($configuration->getSpamFilterEnabled());
    }

    public function testSetRejectRequestsFromBots(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getRejectRequestsFromBots());

        $configuration->setRejectRequestsFromBots(true);
        $this->assertTrue($configuration->getRejectRequestsFromBots());

        $configuration->setRejectRequestsFromBots(false);
        $this->assertFalse($configuration->getRejectRequestsFromBots());
    }

    public function testSetCheckMxOfMailAddress(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getCheckmxOfMailAddress());

        $configuration->setCheckMxOfMailAddress(true);
        $this->assertTrue($configuration->getCheckmxOfMailAddress());

        $configuration->setCheckMxOfMailAddress(false);
        $this->assertFalse($configuration->getCheckmxOfMailAddress());
    }

    public function testSetDisallowChineseChars(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getDisallowChineseChars());

        $configuration->setDisallowChineseChars(true);
        $this->assertTrue($configuration->getDisallowChineseChars());

        $configuration->setDisallowChineseChars(false);
        $this->assertFalse($configuration->getDisallowChineseChars());
    }

    public function testSetDisallowCyrillicChars(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getDisallowCyrillicChars());

        $configuration->setDisallowCyrillicChars(true);
        $this->assertTrue($configuration->getDisallowCyrillicChars());

        $configuration->setDisallowCyrillicChars(false);
        $this->assertFalse($configuration->getDisallowCyrillicChars());
    }

    public function testSetDisallowRtlChars(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getDisallowRtlChars());

        $configuration->setDisallowRtlChars(true);
        $this->assertTrue($configuration->getDisallowRtlChars());

        $configuration->setDisallowRtlChars(false);
        $this->assertFalse($configuration->getDisallowRtlChars());
    }

    public function testSetBlockedCountriesWithString(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBlockedCountries());

        $configuration->setBlockedCountries('ir, vn');
        $this->assertCount(2, $configuration->getBlockedCountries());
        $this->assertContains('ir', $configuration->getBlockedCountries());
        $this->assertContains('vn', $configuration->getBlockedCountries());
    }

    public function testSetBlockedCountriesWithNull(): void {
        $configuration = new SpamFilterConfiguration();

        $configuration->setBlockedCountries(['ir, vn']);

        $configuration->setBlockedCountries(null);
        $this->assertCount(0, $configuration->getBlockedCountries());
    }

    public function testSetBlockedCountriesWithInvalid(): void {
        $configuration = new SpamFilterConfiguration();

        $configuration->setBlockedCountries(['ir, vn']);
        $this->expectException(InvalidArgumentException::class);
        $configuration->setBlockedCountries(new Page());
    }

    public function testSetBlockedCountriesWithArray(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBlockedCountries());

        $configuration->setBlockedCountries([
            'cn',
            'ru',
            'vn'
        ]);
        $this->assertCount(3, $configuration->getBlockedCountries());
        $this->assertContains('cn', $configuration->getBlockedCountries());
        $this->assertContains('ru', $configuration->getBlockedCountries());
        $this->assertContains('vn', $configuration->getBlockedCountries());
    }

    public function testSetBadwordsWithString(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBadwords());

        $configuration->setBadwords("fuck\nshit\nnigger\nbastard");
        $this->assertCount(4, $configuration->getBadwords());
        $this->assertContains('fuck', $configuration->getBadwords());
        $this->assertContains('shit', $configuration->getBadwords());
        $this->assertContains('nigger', $configuration->getBadwords());
        $this->assertContains('bastard', $configuration->getBadwords());
    }

    public function testSetBadwordsWithArray(): void {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBadwords());

        $configuration->setBadwords([
            'fuck',
            'shit',
            'nigger',
            'bastard'
        ]);
        $this->assertCount(4, $configuration->getBadwords());
        $this->assertContains('fuck', $configuration->getBadwords());
        $this->assertContains('shit', $configuration->getBadwords());
        $this->assertContains('nigger', $configuration->getBadwords());
        $this->assertContains('bastard', $configuration->getBadwords());
    }

    public function testSetBadwordsWithNull(): void {
        $configuration = new SpamFilterConfiguration();
        $configuration->setBadwords([
            'fuck',
            'shit',
            'nigger',
            'bastard'
        ]);
        $configuration->setBadwords(null);

        $this->assertCount(0, $configuration->getBadwords());
    }

    public function testSetBadwordsWithInvalid(): void {
        $configuration = new SpamFilterConfiguration();

        $this->expectException(InvalidArgumentException::class);

        $configuration->setBadwords(new Page());

        $this->assertCount($configuration->getBadwords());
    }

    public function testFromSettings(): void {
        $configuration = SpamFilterConfiguration::fromSettings();
        $this->assertInstanceOf(SpamFilterConfiguration::class, $configuration);
        // TODO: Weitere Asserts durchführen
    }
}
