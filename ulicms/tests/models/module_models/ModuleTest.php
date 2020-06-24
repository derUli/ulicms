<?php

class ModuleTest extends \PHPUnit\Framework\TestCase {

	protected function setUp(): void {
		$manager = new ModuleManager();
		$manager->sync();

		$module = new Module("core_comments");
		$module->enable();
	}

	protected function tearDown(): void {
		$manager = new ModuleManager();
		$manager->sync();

		$module = new Module("core_comments");
		$module->enable();
	}

	public function testHasAdminPageReturnsTrue() {
		$module = new Module("fortune2");
		$this->assertTrue($module->hasAdminPage());
	}

	public function testHasAdminPageReturnsFalse() {
		$module = new Module("core_users");
		$this->assertFalse($module->hasAdminPage());
	}

	public function testisEmbedModuleReturnsTrue() {
		$module = new Module("fortune2");
		$this->assertTrue($module->isEmbedModule());
	}

	public function testisEmbedModuleReturnsFalse() {
		$module = new Module("core_users");
		$this->assertFalse($module->isEmbedModule());
	}

	public function testCreateUpdateAndDeleteModule() {
		$module = new Module();
		$module->setName("my_awesome_module");
		$module->setVersion("1.0");
		$module->save();

		$module = new Module("my_awesome_module");
		$this->assertEquals("my_awesome_module", $module->getName());
		$this->assertEquals("1.0", $module->getVersion());
		$this->assertFalse($module->isEnabled());

		$module->setVersion("1.1");
		$module->save();

		$module = new Module("my_awesome_module");

		$this->assertEquals("my_awesome_module", $module->getName());
		$this->assertEquals("1.1", $module->getVersion());
		$this->assertFalse($module->isEnabled());

		$module->enable();
		$this->assertTrue($module->isEnabled());

		$module = new Module("my_awesome_module");
		$this->assertTrue($module->isEnabled());
		$module->disable();


		$module = new Module("my_awesome_module");
		$this->assertFalse($module->isEnabled());

		$this->assertTrue($module->delete());

		// can't delete an already deleted dataset
		$this->assertNull($module->delete());

		$module = new Module("my_awesome_module");
		$this->assertNull($module->getName());
		$this->assertNull($module->getVersion());
	}

	public function testToggleEnabled() {
		$module = new Module("fortune2");
		$module->toggleEnabled();
		$this->assertFalse($module->isEnabled());

		$module = new Module("fortune2");
		$this->assertFalse($module->isEnabled());

		$module->toggleEnabled();

		$this->assertTrue($module->isEnabled());

		$module = new Module("fortune2");
		$this->assertTrue($module->isEnabled());
	}

	public function testIsInstalledReturnsTrue() {
		$module = new Module("core_content");
		$this->assertTrue($module->isInstalled());
	}

	public function testIsInstalledReturnsFalse() {
		$module = new Module("not_existing_module");
		$this->assertFalse($module->isInstalled());
	}

	public function testGetShortcodeReturnsShortcode() {
		$module = new Module();
		$module->setName("hello_world");
		$this->assertEquals("[module=hello_world]", $module->getShortCode());
	}

	public function testGetShortcodeReturnsNull() {
		$module = new Module();
		$this->assertNull(
				$module->getShortCode()
		);
	}

	public function testGetDependentModules() {

		$module = new Module("core_content");

		$this->assertContains(
				"core_comments",
				$module->getDependentModules()
		);
	}

}
