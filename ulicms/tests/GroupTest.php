<?php
class GroupTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->tearDown ();
	}
	public function tearDown() {
		Database::query ( "delete from `{prefix}groups` where name = 'bla'", true );
	}
	public function testCreateGroup() {
		$group = new Group ();
		$this->assertNull ( $group->getId () );
		$group->setName ( "bla" );
		$this->assertEquals ( "bla", $group->getName () );
		$group->save ();
		
		$oldID = $group->getId ();
		$this->assertNotNull ( $oldID );
		$group = new Group ( $oldID );
		$this->assertEquals ( $oldID, $group->getId () );
		$this->assertEquals ( "bla", $group->getName () );
		$this->assertTrue ( is_array ( $group->getPermissions () ) );
		$this->assertTrue ( count ( $group->getPermissions () ) >= 2 );
		$group->delete ();
		$this->assertNull ( $group->getId () );
		$group = new Group ( $oldID );
		$this->assertNull ( $group->getId () );
	}
	public function testGetUsers() {
		$group = new Group ( 1 );
		$this->assertTrue ( count ( $group->getUsers () ) >= 1 );
	}
}