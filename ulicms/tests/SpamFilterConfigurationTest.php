<?php
use UliCMS\Security\SpamChecker\SpamFilterConfiguration;

class SpamFilterConfigurationTest extends \PHPUnit\Framework\TestCase
{

    public function testSetSpamfilterEnabled()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertTrue($configuration->getSpamFilterEnabled());
        
        $configuration->setSpamFilterEnabled(false);
        $this->assertFalse($configuration->getSpamFilterEnabled());
        $configuration->setSpamFilterEnabled(true);
        $this->assertTrue($configuration->getSpamFilterEnabled());
    }

    public function testSetRejectRequestsFromBots()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getRejectRequestsFromBots());
        
        $configuration->setRejectRequestsFromBots(true);
        $this->assertTrue($configuration->getRejectRequestsFromBots());
        
        $configuration->setRejectRequestsFromBots(false);
        $this->assertFalse($configuration->getRejectRequestsFromBots());
    }

    public function testSetCheckMxOfMailAddress()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getCheckmxOfMailAddress());
        
        $configuration->setCheckMxOfMailAddress(true);
        $this->assertTrue($configuration->getCheckmxOfMailAddress());
        
        $configuration->setCheckMxOfMailAddress(false);
        $this->assertFalse($configuration->getCheckmxOfMailAddress());
    }

    public function testSetDisallowChineseChars()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getDisallowChineseChars());
        
        $configuration->setDisallowChineseChars(true);
        $this->assertTrue($configuration->getDisallowChineseChars());
        
        $configuration->setDisallowChineseChars(false);
        $this->assertFalse($configuration->getDisallowChineseChars());
    }

    public function testSetDisallowCyrillicChars()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertFalse($configuration->getDisallowCyrillicChars());
        
        $configuration->setDisallowCyrillicChars(true);
        $this->assertTrue($configuration->getDisallowCyrillicChars());
        
        $configuration->setDisallowCyrillicChars(false);
        $this->assertFalse($configuration->getDisallowCyrillicChars());
    }

    public function testSetBlockedCountriesWithString()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBlockedCountries());
        
        $configuration->setBlockedCountries("ir, vn");
        $this->assertCount(2, $configuration->getBlockedCountries());
        $this->assertContains("ir", $configuration->getBlockedCountries());
        $this->assertContains("vn", $configuration->getBlockedCountries());
    }

    public function testSetBlockedCountriesWithArray()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBlockedCountries());
        
        $configuration->setBlockedCountries(array(
            "cn",
            "ru",
            "vn"
        ));
        $this->assertCount(3, $configuration->getBlockedCountries());
        $this->assertContains("cn", $configuration->getBlockedCountries());
        $this->assertContains("ru", $configuration->getBlockedCountries());
        $this->assertContains("vn", $configuration->getBlockedCountries());
    }

    public function testSetBadwordsWithString()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBadwords());
        
        $configuration->setBadwords("fuck\nshit\nnigger\nbastard");
        $this->assertCount(4, $configuration->getBadwords());
        $this->assertContains("fuck", $configuration->getBadwords());
        $this->assertContains("shit", $configuration->getBadwords());
        $this->assertContains("nigger", $configuration->getBadwords());
        $this->assertContains("bastard", $configuration->getBadwords());
    }

    public function testSetBadwordsWithArray()
    {
        $configuration = new SpamFilterConfiguration();
        $this->assertCount(0, $configuration->getBadwords());
        
        $configuration->setBadwords(array(
            "fuck",
            "shit",
            "nigger",
            "bastard"
        ));
        $this->assertCount(4, $configuration->getBadwords());
        $this->assertContains("fuck", $configuration->getBadwords());
        $this->assertContains("shit", $configuration->getBadwords());
        $this->assertContains("nigger", $configuration->getBadwords());
        $this->assertContains("bastard", $configuration->getBadwords());
    }

    public function testFromString()
    {
        $configuration = SpamFilterConfiguration::fromSettings();
        $this->assertInstanceOf(SpamFilterConfiguration::class, $configuration);
        // TODO: Weitere Asserts durchführen
    }
    
    // TODO: Weitere Tests für Setter und für fromSettings erstellen
}