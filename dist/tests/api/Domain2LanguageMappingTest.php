<?php

class Domain2LanguageMappingTest extends \PHPUnit\Framework\TestCase {
    private string $originalMapping;

    protected function setUp(): void {
        $_SERVER = [];

        $this->originalMapping = Settings::get('domain_to_language');
        $testData = file_get_contents(Path::resolve('ULICMS_ROOT/tests/fixtures/domain2language.txt'));
        Settings::set('domain_to_language', $testData);
    }

    protected function tearDown(): void {
        chdir(ULICMS_ROOT);

        Settings::set('domain_to_language', $this->originalMapping);
    }

    public function testGetLanguageByDomain(): void {
        $this->assertEquals('de', getLanguageByDomain('www.domain.de'));
        $this->assertEquals('en', getLanguageByDomain('domain.com'));
        $this->assertEquals('fr', getLanguageByDomain('domain.fr'));
        $this->assertEquals('it', getLanguageByDomain('domain.it'));
        $this->assertNull(getLanguageByDomain('domain.cn'));
    }

    public function testGetDomainBylanguage(): void {
        $this->assertEquals('www.domain.de', getDomainByLanguage('de'));
        $this->assertEquals('domain.com', getDomainByLanguage('en'));
        $this->assertEquals('domain.fr', getDomainByLanguage('fr'));
        $this->assertEquals('domain.it', getDomainByLanguage('it'));
        $this->assertEquals(null, getDomainByLanguage('cn'));
    }

    public function testSetLanguageByDomainReturnsTrue(): void {
        $_SERVER['HTTP_HOST'] = 'domain.de';
        $this->assertTrue(setLanguageByDomain());
        $this->assertEquals('de', $_SESSION['language']);

        $_SERVER['HTTP_HOST'] = 'domain.com';
        $this->assertTrue(setLanguageByDomain());
        $this->assertEquals('en', $_SESSION['language']);
    }

    public function testSetLanguageByDomainReturnsFalse(): void {
        $_SERVER['HTTP_HOST'] = 'domain.invalid';
        $this->assertFalse(setLanguageByDomain());
    }
}
