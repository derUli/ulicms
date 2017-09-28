<?php
class TodoListItemTest extends PHPUnit_Framework_TestCase {
	public function testCreateUpdateAndDelete() {
		$item = new TodoListItem ();
		$item->setTitle ( "Do Homework" );
		$item->setUserId ( 1 );
		$item->save ();
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
	}
}