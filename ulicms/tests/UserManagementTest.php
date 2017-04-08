<?php
class UserManagementTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$user = new User ();
		$user->loadByUsername ( "max_muster" );
		if (! is_null ( $user->getId () )) {
			$user->delete ();
		}
	}
	public function tearDown() {
		$this->setUp ();
	}
	public function testCreateAndDeleteUser() {
		$user = new User ();
		$user->setUsername ( "max_muster" );
		$user->setFirstname ( "Max" );
		$user->setLastname ( "Muster" );
		$user->setGroupId ( 1 );
		$user->setPassword ( "password123" );
		$user->setEmail ( "max@muster.de" );
		$user->setHomepage ( "http://www.google.de" );
		$user->setSkypeId ( "deruliimnetz" );
		$user->setHTMLEditor ( "ckeditor" );
		$lastLogin = time ();
		$user->setLastLogin ( $lastLogin );
		$user->save ();
		$this->assertNotNull ( $user->getId () );
		$user = new User ();
		$user->loadByUsername ( "max_muster" );
		$this->assertEquals ( "max_muster", $user->getUsername () );
		$this->assertEquals ( "Max", $user->getFirstname () );
		$this->assertEquals ( "Muster", $user->getLastname () );
		$this->assertEquals ( "max@muster.de", $user->getEmail () );
		$this->assertEquals ( 1, $user->getGroupId () );
		$this->assertEquals ( Encryption::hashPassword ( "password123" ), $user->getPassword () );
		$this->assertEquals ( $lastLogin, $user->getLastLogin () );
		$this->assertEquals ( "http://www.google.de", $user->getHomepage () );
		$this->assertEquals ( "deruliimnetz", $user->getSkypeId () );
		$this->assertEquals ( "ckeditor", $user->getHTMLEditor () );
		$user->setHTMLEditor ( "codemirror" );
		$user->save ();
		
		$user = new User ();
		$user->loadByUsername ( "max_muster" );
		$this->assertEquals ( "codemirror", $user->getHTMLEditor () );
		$user->delete ();
		
		$user = new User ();
		$this->assertNull ( $user->getId () );
	}
}