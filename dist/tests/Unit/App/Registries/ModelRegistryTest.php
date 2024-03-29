<?php

use App\Registries\ModelRegistry;

class ModelRegistryTest extends \PHPUnit\Framework\TestCase {
    public function setUp(): void {
        $moduleManager = new \App\Packages\ModuleManager();
        $moduleManager->sync();
    }

    public function testClassLoaded(): void {
        ModelRegistry::loadModuleModels();

        $this->assertTrue(class_exists(HelloWorldModel::class));
    }
}
