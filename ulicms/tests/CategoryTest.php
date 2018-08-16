<?php
class CategoryTest extends \PHPUnit\Framework\TestCase {
	const name1 = "Name 1";
	const name2 = "Name 2";
	const description1 = "Description 1";
	const description2 = "Description 2";
	public function setUp() {
		Database::pQuery ( "delete from `{prefix}categories`
							where name = ? or name = ?", array (
				self::name1,
				self::name2 
		), true );
	}
	public function tearDown() {
		$this->setUp ();
	}
	public function testCreateEditAndDeleteCategory() {
		$category = new Category ();
		$category->setName ( self::name1 );
		$category->setDescription ( self::description1 );
		$category->save ();
		$id = $category->getID ();
		$this->assertNotNull ( $id );
		$category = new Category ( $id );
		$this->assertEquals ( self::name1, $category->getName () );
		$this->assertEquals ( self::description1, $category->getDescription () );
		$this->assertEquals ( $id, $category->getID () );
		$category->setName ( self::name2 );
		$category->setDescription ( self::description2 );
		$category->save ();
		$category = new Category ( $id );
		$this->assertEquals ( self::name2, $category->getName () );
		$this->assertEquals ( self::description2, $category->getDescription () );
		$category->delete ();
		$this->assertNull ( $category->getID () );
		$category = new Category ( $id );
		
		$this->assertNull ( $category->getID () );
	}
}