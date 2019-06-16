<?php

class ActionRegistryTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        ActionRegistry::loadModuleActions();
    }

    public function tearDown() {
        BackendHelper::setAction("home");
    }

    public function testGetDefaultCoreActions() {
        $this->assertArrayHasKey("module_settings", ActionRegistry::getDefaultCoreActions());
    }

    public function testGetActionPermission() {
        $this->assertEquals("community_settings", ActionRegistry::getActionPermission("community_settings"));

        $this->assertNull(ActionRegistry::getActionPermission("my_magic_action"));
    }

    public function testGetControllerReturnsControllerName() {
        BackendHelper::setAction("videos");
        $this->assertInstanceOf(VideoController::class,
                ActionRegistry::getController());

        BackendHelper::setAction("home");
        $this->assertInstanceOf(HomeController::class,
                ActionRegistry::getController());
    }

    public function testGetControllerReturnsNull() {
        BackendHelper::setAction("my_little_pony");
        $this->assertNull(ActionRegistry::getController());
    }

}
