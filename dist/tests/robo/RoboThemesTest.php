<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboTestBase.php";

class RoboThemesTest extends RoboTestBase
{
    protected function setUp(): void
    {
        $this->runRoboCommand(["modules:sync"]);
        $this->runRoboCommand(["cache:clear"]);
    }

    public function testThemesList()
    {
        $output = $this->runRoboCommand(["themes:list"]);
        $this->assertStringContainsString("2020 1.0.4", $output);
        $this->assertStringContainsString("impro17 2.1.6", $output);
    }

    public function testThemesRemove()
    {
        $packageFile = Path::resolve(
            "ULICMS_ROOT/tests/fixtures/packages/theme-2017-1.1.1.tar.gz"
        );
        $this->runRoboCommand(
            ["package:install", $packageFile]
        );

        $this->assertContains("2017", getAllThemes());

        $actual = $this->runRoboCommand(
            [
                "themes:remove",
                "2017",
                "foobar"
            ]
        );

        $this->assertNotContains("2017", getAllThemes());

        $this->assertStringContainsString("Package 2017 removed.", $actual);
        $this->assertStringContainsString("Removing foobar failed.", $actual);
    }
}
