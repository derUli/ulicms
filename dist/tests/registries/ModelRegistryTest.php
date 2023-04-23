<?php

use App\Registries\ModelRegistry;

class ModelRegistryTest extends \PHPUnit\Framework\TestCase {
    public function setUp(): void {
        $moduleManager = new ModuleManager();
        $moduleManager->sync();
    }

    public function testClassLoaded() {
        ModelRegistry::loadModuleModels();

        $this->assertTrue(class_exists(HelloWorldModel::class));
    }
}
