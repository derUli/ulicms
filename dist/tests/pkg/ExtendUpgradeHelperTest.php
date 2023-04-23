<?php

use Fetcher\Fetcher;

class ExtendUpgradeHelperTest extends \PHPUnit\Framework\TestCase {
    public function testExtendUpgradeHelper() {
        if (! class_exists('ExtendUpgradeHelper')) {
            $this->markTestSkipped('extend_upgrade_helper is not installed');
        }
        $helper = new ExtendUpgradeHelper();
        $modules = $helper->getModules();

        $this->assertGreaterThanOrEqual(3, count($modules));

        $actualModuleNames = [];

        foreach ($modules as $module) {
            $actualModuleNames[] = $module->name;
            $this->assertNotEmpty($module->name);
            $this->assertNotEmpty($module->version);
            $this->assertEquals(
                "https://extend.ulicms.de/{$module->name}.html",
                $module->url
            );

            $this->assertTrue(Fetcher::isUrl($module->url));
        }
        foreach ($this->getExpectedModuleNames() as $expectedModuleName) {
            $this->assertContains($expectedModuleName, $actualModuleNames);
        }
    }

    private function getExpectedModuleNames(): array {
        return [
            'oneclick_upgrade',
            'fortune2',
        ];
    }
}
