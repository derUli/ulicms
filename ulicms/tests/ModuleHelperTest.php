<?php
class ModuleHelperTest extends PHPUnit_Framework_TestCase {
	private $default_language = null;
	public function setUp() {
		$this->default_language = Settings::get ( "default_language" );
	}
	public function tearDown() {
		Settings::set ( "default_language", $this->default_language );
	}
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
		Settings::set ( "default_language", "de" );
		$this->assertEquals ( 6, ModuleHelper::getFirstPageWithModule ()->id );
		$this->assertEquals ( 7, ModuleHelper::getFirstPageWithModule ( "pfbc_sample" )->id );
		$this->assertEquals ( 6, ModuleHelper::getFirstPageWithModule ( "fortune2" )->id );
		$this->assertEquals ( 7, ModuleHelper::getFirstPageWithModule ( "pfbc_sample", "de" )->id );
		$this->assertEquals ( 6, ModuleHelper::getFirstPageWithModule ( "fortune2", "de" )->id );
		$this->assertEquals ( 14, ModuleHelper::getFirstPageWithModule ( "pfbc_sample", "en" )->id );
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ( "fortune2", "en" )->id );
		$this->assertEquals ( 6, ModuleHelper::getFirstPageWithModule ( null, "de" )->id );
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ( null, "en" )->id );
		
		Settings::set ( "default_language", "en" );
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ()->id );
		$this->assertEquals ( 14, ModuleHelper::getFirstPageWithModule ( "pfbc_sample" )->id );
		$this->assertEquals ( 13, ModuleHelper::getFirstPageWithModule ( "fortune2" )->id );
	}
	public function testIsEmbedModule() {
		$this->assertTrue ( ModuleHelper::isEmbedModule ( "fortune" ) );
		$this->assertFalse ( ModuleHelper::isEmbedModule ( "slicknav" ) );
	}
	public function testGetAllEmbedModule() {
		$embedModules = ModuleHelper::getAllEmbedModules ();
		$this->assertTrue ( in_array ( "fortune2", $embedModules ) );
		$this->assertFalse ( in_array ( "slicknav", $embedModules ) );
	}
	public function testGetMainController() {
		$this->assertInstanceOf ( "Fortune", ModuleHelper::getMainController ( "fortune2" ) );
		$this->assertNull ( ModuleHelper::getMainController ( "slicknav" ) );
		$this->assertNull ( ModuleHelper::getMainController ( "not_a_module" ) );
	}
}