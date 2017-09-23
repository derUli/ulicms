<?php
class AudioTest extends PHPUnit_Framework_TestCase {
	public function testCreateAndDelete() {
		$audio = new Audio ();
		$audio->setName ( "My Name" );
		$audio->setMP3File ( "music.mp3" );
		$audio->setOGGFile ( "music.ogg" );
		$audio->setCategoryId ( 1 );
		$audio->save ();
		$this->assertNotNull ( $audio->getID () );
		$audio->delete ();
		$this->assertNull ( $audio->getID () );
		$audio = new Audio ();
		$this->assertNull ( $audio->getID () );
	}
}