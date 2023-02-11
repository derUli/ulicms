<?php

use App\Utils\CacheUtil;

class InfoControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        Translation::loadAllModuleLanguageFiles("en");
        CacheUtil::clearCache();
    }

    protected function tearDown(): void
    {
        CacheUtil::clearCache();
    }

    public function testfetchChangelog()
    {
        $controller = new InfoController();
        $this->assertStringContainsString(
            "Neues in UliCMS 2020",
            $controller->_fetchChangelog()
        );
    }

    public function testGetComposerLegalInfo()
    {
        $controller = new InfoController();
        $legalInfo = $controller->_getComposerLegalInfo();
        $this->assertStringContainsString(
            "<h1>Composer Licenses</h1>",
            $legalInfo
        );

        $legalInfo = $controller->_getComposerLegalInfo();
        $this->assertGreaterThanOrEqual(132000, strlen($legalInfo));
    }

    public function testGetNpmLegalInfo()
    {
        $controller = new InfoController();
        $npmLegalData = $controller->_getNpmLegalInfo();

        $this->assertCount(15, $npmLegalData);

        foreach ($npmLegalData as $package) {
            $this->assertNotEmpty($package->name);
            $this->assertNotEmpty($package->licenseType);
        }
    }
}
