<?php
class ApiTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Database::query ( "delete from `{prefix}categories` where name = 'my_category'", true );
		Database::query ( "delete from `{prefix}content` where systemname = 'my_title'", true );
	}
	public function tearDown() {
		$this->setUp ();
	}
	public function testGetFirstPageInCategory() {
		$category = new Category ();
		$category->setName ( "my_category" );
		$category->save ();
		$originalPage1 = new Page ();
		$originalPage1->menu = "hidden";
		$originalPage1->access = "all";
		$originalPage1->language = "de";
		$originalPage1->title = "my_page";
		$originalPage1->systemname = "my_title";
		$originalPage1->category = $category->getID ();
		$originalPage1->save ();
		
		$originalPage2 = new Page ();
		$originalPage2->title = "my_page";
		$originalPage2->systemname = "my_title";
		$originalPage2->menu = "hidden";
		$originalPage2->access = "all";
		$originalPage2->language = "en";
		$originalPage2->category = $category->getID ();
		$originalPage2->save ();
		$firstInCategory = ModuleHelper::getMainController ( "first_in_category" );
		$page = $firstInCategory->getFirstPageInCategory ( $category->getID (), "de" );
		$this->assertEquals ( $originalPage1->getID (), $page->id );
		$this->assertEquals ( $originalPage1->title, "my_page" );
		$page = $firstInCategory->getFirstPageInCategory ( $category->getID (), "en" );
		$this->assertEquals ( $originalPage2->getID (), $page->id );
		$this->assertEquals ( $originalPage1->title, "my_page" );
	}
}
