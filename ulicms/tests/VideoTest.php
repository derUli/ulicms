<?php
class VideoTest extends PHPUnit_Framework_TestCase {
	public function testCreateUpdateAndDelete() {
		$Video = new Video ();
		$Video->setName ( "My Name" );
		$Video->setMP4File ( "video.mp4" );
		$Video->setOGGFile ( "video.ogv" );
		$Video->setWebmFile ( "video.webm" );
		$Video->setCategoryId ( 1 );
		$Video->save ();
		$this->assertNotNull ( $Video->getID () );
		$id = $Video->getId ();
		$Video = new Video ( $id );
		$this->assertNotNull ( $Video->getID () );
		$this->assertEquals ( "My Name", $Video->getName () );
		$this->assertEquals ( "video.mp4", $Video->getMP4File () );
		$this->assertEquals ( "video.ogv", $Video->getOggFile () );
		$this->assertEquals ( "video.webm", $Video->getWebmFile () );
		$this->assertEquals ( 1, $Video->getCategoryId () );
		$this->assertEquals ( 1, $$Video->getCategory ()->getID () );
		
		$Video->setName ( "New Name" );
		$Video->setMP4File ( "not-video.mp4" );
		$Video->setOGGFile ( "not-video.ogg" );
		$Video->setWebmFile ( "not-video.webm" );
		$Video->setCategoryId ( null );
		$Video->save ();
		$Video = new Video ( $id );
		
		$this->assertEquals ( "New Name", $Video->getName () );
		$this->assertEquals ( "not-video.mp4", $Video->getMP4File () );
		$this->assertEquals ( "not-video.ogg", $Video->getOggFile () );
		$this->assertEquals ( "not-video.webm", $Video->getWebmFile () );
		$this->assertEquals ( null, $Video->getCategoryId () );
		
		$Video = new Video ( $id );
		
		$video->setCategory ( new Group ( 1 ) );
		$video->save ();
		
		$video = new Video ( $id );
		
		$this->assertEquals ( 1, $video->getCategoryId () );
		$this->assertEquals ( 1, $video->getCategory ()->getID () );
		
		$Video->delete ();
		$this->assertNull ( $Video->getID () );
		$Video = new Video ();
		$this->assertNull ( $Video->getID () );
	}
	public function testVideoHtml() {
		$Video = new Video ();
		$Video->setName ( "My Name" );
		$Video->setMP4File ( "video.mp4" );
		$Video->setOGGFile ( "video.ogv" );
		$Video->setWebmFile ( "video.webm" );
		$Video->setCategoryId ( 1 );
		$this->assertEquals ( '<video width="" height="" controls><source src="content/videos/video.mp4" type="video/mp4"><source src="content/videos/video.ogv" type="video/ogg"><source src="content/videos/video.webm" type="video/webm">no_html5<br/><a href="content/videos/">DOWNLOAD_VIDEO_INSTEAD</a></video>', $Video->getHtml () );
	}
}