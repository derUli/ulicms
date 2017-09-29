<?php
class TodoListItemTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Database::query ( "delete from `{prefix}todolist_items`
 						where title = 'Do Homework' or title = 'Clean the bathroom'", true );
	}
	public function tearDown() {
		$this->setUp ();
	}
	public function testCreateUpdateAndDelete() {
		$this->assertEquals ( 1, PositionHelper::getNextFreePosition ( 1 ) );
		$item = new TodoListItem ();
		$item->setTitle ( "Do Homework" );
		$item->setUserId ( 1 );
		$item->save ();
		$this->assertEquals ( 2, PositionHelper::getNextFreePosition ( 1 ) );
		$this->assertNotNull ( $item->getID () );
		$id = $item->getID ();
		$item = new TodoListItem ( $id );
		$this->assertNotNull ( $item->getID () );
		$this->assertEquals ( "Do Homework", $item->getTitle () );
		$this->assertEquals ( 1, $item->getUserID () );
		$this->assertFalse ( $item->isDone () );
		
		$item->setTitle ( "Clean the bathroom" );
		$item->setDone ( true );
		$item->save ();
		$item = new TodoListItem ( $id );
		$this->assertNotNull ( $item->getID () );
		$this->assertEquals ( "Clean the bathroom", $item->getTitle () );
		
		$this->assertTrue ( $item->isDone () );
		$item->delete ();
		$this->assertNull ( $item->getID () );
		$item = new TodoListItem ( $id );
		$this->assertNull ( $item->getID () );
		$this->assertEquals ( 1, PositionHelper::getNextFreePosition () );
	}
}