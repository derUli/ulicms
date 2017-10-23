<?php
class UserSettingsTest extends PHPUnit_Framework_TestCase {
	private $user1 = null;
	private $user2 = null;
	public function setUp() {
		@session_start ();
		$user = new User ();
		$user->setUsername ( "user1" );
		$user->setFirstname ( "Max" );
		$user->setLastname ( "Mustermann" );
		$user->setPassword ( md5 ( uniqid () ) );
		$user->save ();
		$this->user1 = $user;
		$user = new User ();
		$user->setUsername ( "user2" );
		$user->setFirstname ( "Erika" );
		$user->setLastname ( "Mustermann" );
		$user->setPassword ( md5 ( uniqid () ) );
		$user->save ();
		$this->user2 = $user;
	}
	public function tearDown() {
		$this->user1->delete ();
		$this->user2->delete ();
		@session_destroy ();
	}
	public function testUserSettings() {
		$user_ids = array (
				$this->user1->getId (),
				$this->user2->getId () 
		);
		foreach ( $user_ids as $uid ) {
			$_SESSION ["login_id"] = $uid;
			UserSettings::delete ( "example_setting" );
			$this->assertEquals ( null, UserSettings::get ( "example_setting" ) );
			
			UserSettings::register ( "example_setting", "hello" );
			$this->assertEquals ( "hello", UserSettings::get ( "example_setting" ) );
			
			UserSettings::register ( "example_setting", "bye" );
			$this->assertEquals ( "hello", UserSettings::get ( "example_setting" ) );
			
			UserSettings::set ( "example_setting", "bye" );
			$this->assertEquals ( "bye", UserSettings::get ( "example_setting" ) );
			
			UserSettings::delete ( "example_setting" );
			$this->assertEquals ( null, UserSettings::get ( "example_setting" ) );
		}
	}
	public function testDifferentUsersSameSettings() {
		$this->assertNull ( UserSettings::get ( "my_option", null, $this->user1 ) );
		$this->assertNull ( UserSettings::get ( "my_option", null, $this->user1 ) );
		UserSettings::set ( "my_option", "hello", null, $this->user1->getId () );
		UserSettings::set ( "my_option", "world", null, $this->user2->getId () );
		$this->assertEquals ( "hello", UserSettings::get ( "my_option", null, $this->user1->getId () ) );
		$this->assertEquals ( "world", UserSettings::get ( "my_option", null, $this->user2->getId () ) );
	}
}