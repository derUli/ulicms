<?php

require_once __DIR__ . "/RoboTestFile.php";
require_once __DIR__ . "/RoboBaseTest.php";

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\TaskAccessor;

class ModulesRoboTest extends RoboBaseTest {

    public function setUp() {
        $this->runRoboCommand(["modules:sync"]);
    }

    public function testModulesList() {
        $output = $this->runRoboCommand(["modules:list"]);

        $this->assertEquals(13, substr_count($output, "core_"));
        foreach (getAllModules() as $module) {
            $this->assertStringContainsString($module, $output);
        }
    }

    public function testModulesGetPackageVersions() {
        $expected = file_get_contents(
                Path::resolve(
                        "ULICMS_ROOT/tests/fixtures/robo/modulesGetPackageVersions.expected.txt"
                )
        );

        $actual = $this->runRoboCommand(
                ["modules:get-package-versions",
                    "ldap_login"
                ]
        );
        $this->assertEquals($expected, $actual);
    }

}
