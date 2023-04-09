<?php

require_once __DIR__ . '/RoboTestFile.php';
require_once __DIR__ . '/RoboTestBase.php';

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\TaskAccessor;
use App\Constants\DefaultValues;

class RoboSettingsTest extends RoboTestBase
{
    protected function tearDown(): void
    {
        Settings::delete('foo1');
        Settings::delete('foo2');
        Settings::delete('');
    }

    public function testSettingsList()
    {
        Settings::set('', '');
        Settings::set('foo2', '');
        $output = $this->runRoboCommand(['settings:list']);
        $this->assertGreaterThanOrEqual(60, substr_count($output, "\n"));
        $allSettings = Settings::getAll();
        foreach ($allSettings as $setting) {
            if (!$setting->name) {
                continue;
            }

            $this->assertStringContainsString(
                "$setting->name: $setting->value",
                $output
            );
        }
    }

    public function testSettingsPrintsString()
    {
        Settings::set('foo1', 'Hello World');
        $output = $this->runRoboCommand(['settings:get', 'foo1']);
        $this->assertStringContainsString('Hello World', $output);
    }

    public function testSettingsPrintsNull()
    {
        $output = $this->runRoboCommand(['settings:get', 'gibts_nicht']);
        $this->assertStringContainsString(DefaultValues::NULL_VALUE, $output);
    }

    public function testSettingsSetToValue()
    {
        $this->runRoboCommand(
            [
                'settings:set',
                'foo1',
                'Moin Moin']
        );

        $this->assertEquals(
            'Moin Moin',
            Settings::get('foo1')
        );

        $this->runRoboCommand(
            [
                'settings:set',
                'foo1',
                DefaultValues::NULL_VALUE
            ]
        );
        $this->assertNull(Settings::get('foo1'));
    }
}
