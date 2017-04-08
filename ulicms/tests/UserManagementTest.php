<?php
class UserManagementTest extends PHPUnit_Framework_TestCase {
	public function testCreateAndDeleteUser() {
		$user = new User ();
		$user->setUsername ( "max_muster" );
		$user->setFirstname ( "Max" );
		$user->setLastname ( "Muster" );
		$user->setGroupId ( 1 );
		$user->setPassword ( "password123" );
		$user->save ();
		$this->assertNotNull ( $user->getId () );
		$user = new User ();
		$user->loadByUsername ( "max_muster" );
		$this->assertEquals ( "max_muster", $user->getUsername () );
		$this->assertEquals ( "Max", $user->getFirstname () );
		$this->assertEquals ( "Muster", $user->getLastname () );
		$this->assertEquals ( 1, $user->getGroupId () );
		$this->assertEquals ( Encryption::hashPassword ( "password123" ), $user->getPassword () );
		$user->delete ();
		
		$user = new User ();
		$this->assertNull ( $user->getId () );
	}
}