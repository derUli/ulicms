<?php
class UsersApiTest extends \PHPUnit\Framework\TestCase {
	public function setUp(){
		@session_start();
		unset($_SESSION["login_id"]);
	}
	public function tearDown(){
		unset($_SESSION["login_id"]);
	}
	public function testGetUserIdUserIsLoggedIn(){
		$_SESSION["login_id"] = 123;
		$this->assertEquals(123, get_user_id());
		unset($_SESSION["login_id"]);
	}
	public function testGetUserIdUserIsNotLoggedIn(){
		$this->assertEquals(0, get_user_id());
	}
}