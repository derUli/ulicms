<?php
class ModuleManagerTest extends PHPUnit_Framework_TestCase {
	const sampleName1 = "mymodule1";
	const sampleName2 = "mymodule2";
	const sampleVersion1 = "1.0";
	const sampleVersion2 = "2.0";
	public function testCreateAndEditModule1() {
		$manager = new ModuleManager ();
		
		$module = new Module ();
		$module->setName ( self::sampleName1 );
		$module->setVersion ( self::sampleVersion1 );
		$module->save ();
		
		$allModules = $manager->getAllModuleNames ();
		
		$this->assertTrue ( in_array ( self::sampleName1, $allModules ) );
		
		$module = new Module ( self::sampleName1 );
		$this->assertEquals ( self::sampleName1, $module->getName () );
		$this->assertEquals ( self::sampleVersion1, $module->getVersion () );
		$this->assertFalse ( $module->isEnabled () );
		$module->setVersion ( self::sampleVersion2 );
		$module->save ();
		
		$module = new Module ( self::sampleName1 );
		$this->assertEquals ( self::sampleVersion2, $module->getVersion () );
		$module->enable ();
		
		$module = new Module ( self::sampleName1 );
		
		$this->assertTrue ( $module->isEnabled () );
		$module->disable ();
		
		$module = new Module ( self::sampleName1 );
		$this->assertFalse ( $module->isEnabled () );
		
		$module = new Module ( self::sampleName1 );
		
		$module = new Module ();
		$module->setName ( self::sampleName2 );
		$module->setVersion ( self::sampleVersion2 );
		$module->save ();
		
		$allModules = $manager->getAllModuleNames ();
		
		$this->assertTrue ( in_array ( self::sampleName1, $allModules ) );
		$this->assertTrue ( in_array ( self::sampleName2, $allModules ) );
		
		$module = new Module ( self::sampleName1 );
		$module->delete ();
		
		$allModules = $manager->getAllModuleNames ();
		$this->assertFalse ( in_array ( self::sampleName1, $allModules ) );
		
		$module = new Module ( self::sampleName2 );
		$module->delete ();
		
		$allModules = $manager->getAllModuleNames ();
		$this->assertFalse ( in_array ( self::sampleName2, $allModules ) );
	}
	
	// TODO: Test für sync() implementieren
	public function testSync() {
	}
}