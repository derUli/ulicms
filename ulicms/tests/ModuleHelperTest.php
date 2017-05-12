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
}