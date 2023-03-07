<?php

use App\Registries\ActionRegistry;

class ActionRegistryTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();

        ActionRegistry::loadModuleActions();
    }

    protected function tearDown(): void
    {
        BackendHelper::setAction("home");
    }

    public function testGetDefaultCoreActions()
    {
        $this->assertArrayHasKey("module_settings", ActionRegistry::getDefaultCoreActions());
    }

    public function testGetActionPermission()
    {
        $this->assertEquals("community_settings", ActionRegistry::getActionPermission("community_settings"));

        $this->assertNull(ActionRegistry::getActionPermission("my_magic_action"));
    }

    public function testGetControllerReturnsControllerName()
    {
        BackendHelper::setAction("videos");
        $this->assertInstanceOf(
            VideoController::class,
            ActionRegistry::getController()
        );

        BackendHelper::setAction("home");
        $this->assertInstanceOf(
            HomeController::class,
            ActionRegistry::getController()
        );
    }

    public function testGetControllerReturnsNull()
    {
        BackendHelper::setAction("my_little_pony");
        $this->assertNull(ActionRegistry::getController());
    }

    public function testGetControllerByCurrentActionReturnsController()
    {
        ActionRegistry::assignControllerToAction(
            "foobar",
            HistoryController::class
        );

        BackendHelper::setAction("foobar");
        $this->assertInstanceOf(HistoryController::class, ActionRegistry::getController());
    }

    public function testGetActions()
    {
        $actions = ActionRegistry::getActions();
        $this->assertGreaterThanOrEqual(60, count($actions));
        $this->assertStringEndsWith("content/modules/core_content/templates/contents.php", $actions["contents"]);
        foreach (array_values($actions) as $file) {
            $this->assertFileExists($file);
            $this->assertStringEndsWith('.php', $file);
        }
    }

    public function testGetAction()
    {
        $file = ActionRegistry::getAction("comments_manage");
        $this->assertStringEndsWith("content/modules/core_comments/templates/admin.php", $file);
        $this->assertFileExists($file);
    }
}
