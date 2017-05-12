<?php
class ModuleHelperTest extends PHPUnit_Framework_TestCase {
	public function testUnderscoreToCamel() {
		$this->assertEquals ( "myModuleName", ModuleHelper::underscoreToCamel ( "my_module_name" ) );
		$this->assertEquals ( "init", ModuleHelper::underscoreToCamel ( "init" ) );
		$this->assertEquals ( "myModuleName", ModuleHelper::underscoreToCamel ( "My_Module_Name" ) );
	}
	public function testBuildModuleRessourcePath() {
		$this->assertEquals ( "content/modules/my_module/js/coolscript.js", ModuleHelper::buildModuleRessourcePath ( "my_module", "js/coolscript.js" ) );
		$this->assertEquals ( "content/modules/other_module/test.css", ModuleHelper::buildModuleRessourcePath ( "other_module", "test.css" ) );
	}
	public function testBuildAdminURL() {
		$this->assertEquals ( "?action=module_settings&module=my_module&var1=hallo&var2=welt", ModuleHelper::buildAdminURL ( "my_module", "var1=hallo&var2=welt" ) );
		$this->assertEquals ( "?action=module_settings&module=other_module", ModuleHelper::buildAdminURL ( "other_module" ) );
	}
	public function testGetFirstPageWithModule() {
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ()->id );
		$this->assertEquals ( 14, ModuleHelper::getFirstPageWithModule ( "pfbc_sample" )->id );
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ( "fortune" )->id );
		$this->assertEquals ( 7, ModuleHelper::getFirstPageWithModule ( "pfbc_sample", "de" )->id );
		$this->assertEquals ( 6, ModuleHelper::getFirstPageWithModule ( "fortune", "de" )->id );
		$this->assertEquals ( 14, ModuleHelper::getFirstPageWithModule ( "pfbc_sample", "en" )->id );
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ( "fortune", "en" )->id );
		$this->assertEquals ( 6, ModuleHelper::getFirstPageWithModule ( null, "de" )->id );
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ( null, "en" )->id );
	}
	public function testIsEmbedModule() {
		$this->assertTrue ( ModuleHelper::isEmbedModule ( "fortune" ) );
		$this->assertFalse ( ModuleHelper::isEmbedModule ( "slicknav" ) );
	}
	public function testIsEmbedModule() {
		$embedModules = ModuleHelper::getAllEmbedModules ();
		$this->assertTrue ( in_array ( "fortune", $embedModules ) );
		$this->assertFalse ( in_array ( "slicknav", $embedModules ) );
	}
}