<?php

class Domain2LanguageMappingTest extends \PHPUnit\Framework\TestCase {

    private $originalMapping;

    public function setUp() {
        $_SERVER = [];
        
                $this->originalMapping = Settings::get("domain_to_language");
        $testData = file_get_contents(Path::resolve("ULICMS_ROOT/tests/fixtures/domain2language.txt"));
        Settings::set("domain_to_language", $testData);
    }

    public function tearDown() {
        chdir(ULICMS_ROOT);

        Settings::set("domain_to_language", $this->originalMapping);
       
            }

    public function testGetLanguageByDomain() {
        $this->assertEquals("de", getLanguageByDomain("www.domain.de"));
        $this->assertEquals("en", getLanguageByDomain("domain.com"));
        $this->assertEquals("fr", getLanguageByDomain("domain.fr"));
        $this->assertEquals("it", getLanguageByDomain("domain.it"));
        $this->assertNull(getLanguageByDomain("domain.cn"));
    }

    public function testGetDomainBylanguage() {
        $this->assertEquals("www.domain.de", getDomainByLanguage("de"));
        $this->assertEquals("domain.com", getDomainByLanguage("en"));
        $this->assertEquals("domain.fr", getDomainByLanguage("fr"));
        $this->assertEquals("domain.it", getDomainByLanguage("it"));
        $this->assertEquals(null, getDomainByLanguage("cn"));
    }

    public function testSetLanguageByDomainReturnsTrue() {
        $_SERVER["HTTP_HOST"] = "domain.de";
        $this->assertTrue(setLanguageByDomain());
        $this->assertEquals("de", $_SESSION["language"]);

        $_SERVER["HTTP_HOST"] = "domain.com";
        $this->assertTrue(setLanguageByDomain());
        $this->assertEquals("en", $_SESSION["language"]);
    }

    public function testSetLanguageByDomainReturnsFalse() {
        $_SERVER["HTTP_HOST"] = "domain.invalid";
        $this->assertFalse(setLanguageByDomain());
    }

    public function testSetLocaleByLanguageFrontend() {

        $_SESSION["language"] = "de";
        $languages = setLocaleByLanguage();

        $this->assertEquals(
                [
                    LC_ALL,
                    "de_DE.UTF-8",
                    "de_DE",
                    "deu_deu"
                ],
                $languages
        );
    }

    public function testSetLocaleByLanguageBackend() {
        chdir(Path::resolve("ULICMS_ROOT/admin"));

        $_SESSION["system_language"] = "en";
        $languages = setLocaleByLanguage();

        $this->assertEquals(
                [
                    LC_ALL,
                    "en_US.UTF-8",
                    "en_GB.UTF-8",
                    "en_US",
                    "en_GB",
                    "english-uk",
                    "eng",
                    "uk"
                ],
                $languages
        );
    }

}
