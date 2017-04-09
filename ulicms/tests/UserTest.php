<?php
class UserTest extends PHPUnit_Framework_TestCase {
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
		$user->setTwitter ( "ulicms" );
		$user->setAboutMe ( "hello world" );
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
		$this->assertEquals ( false, $user->getRequirePasswordChange () );
		$this->assertEquals ( false, $user->getNotifyOnLogin () );
		$this->assertEquals ( false, $user->getAdmin () );
		$this->assertEquals ( false, $user->getLocked () );
		$this->assertEquals ( "hello world", $user->getAboutMe () );
		$this->assertEquals ( "ulicms", $user->getTwitter () );
		$user->setHTMLEditor ( "codemirror" );
		$user->setNotifyOnLogin ( true );
		$user->setRequirePasswordChange ( true );
		$user->setLocked ( true );
		$user->setAdmin ( true );
		$user->setAboutMe ( "bye" );
		$user->save ();
		
		$user = new User ();
		$user->loadByUsername ( "max_muster" );
		$this->assertEquals ( "codemirror", $user->getHTMLEditor () );
		$this->assertEquals ( true, $user->getNotifyOnLogin () );
		
		$this->assertEquals ( true, $user->getLocked () );
		$this->assertEquals ( true, $user->getAdmin () );
		$this->assertEquals ( true, $user->getRequirePasswordChange () );
		$this->assertEquals ( "bye", $user->getAboutMe () );
		$user->delete ();
		
		$user = new User ();
		$this->assertNull ( $user->getId () );
	}
}