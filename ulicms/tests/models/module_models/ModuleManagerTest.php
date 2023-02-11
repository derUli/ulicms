<?php

class ModuleManagerTest extends \PHPUnit\Framework\TestCase
{
    public const sampleName1 = "mymodule1";
    public const sampleName2 = "mymodule2";
    public const sampleVersion1 = "1.0";
    public const sampleVersion2 = "2.0";

    private $oneclick_upgrade_channel;

    protected function setUp(): void
    {
        $this->oneclick_upgrade_channel = Settings::get("oneclick_upgrade_channel");

        Database::truncateTable("modules");
    }

    protected function tearDown(): void
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $module = new Module("fortune2");
        $module->enable();

        Settings::set("oneclick_upgrade_channel", $this->oneclick_upgrade_channel);
    }

    public function testCreateAndEditModule1()
    {
        $manager = new ModuleManager();

        $module = new Module();
        $module->setName(self::sampleName1);
        $module->setVersion(self::sampleVersion1);
        $module->save();

        $allModules = $manager->getAllModuleNames();

        $this->assertTrue(in_array(self::sampleName1, $allModules));

        $module = new Module(self::sampleName1);
        $this->assertEquals(self::sampleName1, $module->getName());
        $this->assertEquals(self::sampleVersion1, $module->getVersion());
        $this->assertFalse($module->isEnabled());
        $module->setVersion(self::sampleVersion2);
        $module->save();

        $module = new Module(self::sampleName1);
        $this->assertEquals(self::sampleVersion2, $module->getVersion());
        $module->enable();

        $module = new Module(self::sampleName1);

        $this->assertTrue($module->isEnabled());
        $module->disable();

        $module = new Module(self::sampleName1);
        $this->assertFalse($module->isEnabled());

        $module = new Module(self::sampleName1);

        $module = new Module();
        $module->setName(self::sampleName2);
        $module->setVersion(self::sampleVersion2);
        $module->save();

        $allModules = $manager->getAllModuleNames();

        $this->assertTrue(in_array(self::sampleName1, $allModules));
        $this->assertTrue(in_array(self::sampleName2, $allModules));

        $module = new Module(self::sampleName1);
        $module->delete();

        $allModules = $manager->getAllModuleNames();
        $this->assertFalse(in_array(self::sampleName1, $allModules));

        $module = new Module(self::sampleName2);
        $module->delete();

        $allModules = $manager->getAllModuleNames();
        $this->assertFalse(in_array(self::sampleName2, $allModules));
    }

    public function testInitialSync()
    {
        $manager = new ModuleManager();

        Database::query("truncate table {prefix}modules", true);
        $this->assertEquals(0, count($manager->getAllModules()));
        $manager->sync();
        $this->assertGreaterThanOrEqual(19, count($manager->getAllModules()));
        $this->assertEquals(count(getAllModules()), count($manager->getAllModules()));
    }

    public function testGetDisabledModuleNames()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $module = new Module("fortune2");
        $module->disable();

        $manager = new ModuleManager();
        $this->assertContains("fortune2", $manager->getDisabledModuleNames());

        $module->enable();

        $this->assertNotContains(
            "fortune2",
            $manager->getDisabledModuleNames()
        );
    }

    public function testRemoveDeletedModules()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $module = new Module();
        $module->setName("wurde_geloescht");
        $module->setVersion("1.0");
        $module->save();

        $this->assertContains("wurde_geloescht", $moduleManager->getAllModuleNames());
        $moduleManager->sync();

        $this->assertNotContains("wurde_geloescht", $moduleManager->getAllModuleNames());
    }

    public function testUpdateModuleVersion()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $module = new Module("Mobile_Detect");
        $module->setVersion("1.0");
        $module->save();

        $this->assertEquals("1.0", $module->getVersion());

        $moduleManager->sync();

        $module = new Module("Mobile_Detect");
        $this->assertEquals("3.74.0", $module->getVersion());
    }

    public function testInitModulesDefaultSettings()
    {
        Settings::delete("oneclick_upgrade_channel");

        $this->assertNull(Settings::get("oneclick_upgrade_channel"));

        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $this->assertEquals("slow", Settings::get("oneclick_upgrade_channel"));
    }

    public function testGetDependencies()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $this->assertContains(
            "core_content",
            $moduleManager->getDependencies("core_comments")
        );
    }

    public function testGetDependentModules()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        $this->assertContains(
            "core_comments",
            $moduleManager->getDependentModules("core_content")
        );
    }
}
