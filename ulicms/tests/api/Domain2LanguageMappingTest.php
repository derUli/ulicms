<?php

class Domain2LanguageMappingTest extends \PHPUnit\Framework\TestCase {

    private $originalMapping;
    private $originalHTTPHost;

    public function setUp() {
        @session_start();
        $this->originalMapping = Settings::get("domain_to_language");
        $testData = file_get_contents(Path::resolve("ULICMS_ROOT/tests/fixtures/domain2language.txt"));
        $this->originalHTTPHost = $_SERVER["HTTP_HOST"];
        Settings::set("domain_to_language", $testData);
    }

    public function tearDown() {
        Settings::set("domain_to_language", $this->originalMapping);
        $_SERVER["HTTP_HOST"] = $this->originalHTTPHost;
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

    public function testSetLanguageByDomain() {
        $_SERVER["HTTP_HOST"] = "domain.de";
        setLanguageByDomain();
        $this->assertEquals("de", $_SESSION["language"]);

        $_SERVER["HTTP_HOST"] = "domain.com";
        setLanguageByDomain();
        $this->assertEquals("en", $_SESSION["language"]);
    }

}
