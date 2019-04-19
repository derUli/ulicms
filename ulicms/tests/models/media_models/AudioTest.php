<?php

class AudioTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        require_once getLanguageFilePath("en");
    }

    public function testCreateUpdateAndDelete() {
        $audio = new Audio ();
        $audio->setName("My Name");
        $audio->setMP3File("music.mp3");
        $audio->setOGGFile("music.ogg");
        $audio->setCategoryId(1);
        $audio->save();
        $this->assertNotNull($audio->getID());
        $id = $audio->getId();
        $audio = new Audio($id);
        $this->assertNotNull($audio->getID());
        $this->assertEquals("My Name", $audio->getName());
        $this->assertEquals("music.mp3", $audio->getMP3File());
        $this->assertEquals("music.ogg", $audio->getOggFile());
        $this->assertEquals(1, $audio->getCategoryId());
        $this->assertEquals(1, $audio->getCategory()->getID());

        $audio->setName("New Name");
        $audio->setMP3File("not-music.mp3");
        $audio->setOGGFile("not-music.ogg");
        $audio->setCategoryId(null);
        $audio->save();
        $audio = new Audio($id);

        $this->assertEquals("New Name", $audio->getName());
        $this->assertEquals("not-music.mp3", $audio->getMP3File());
        $this->assertEquals("not-music.ogg", $audio->getOggFile());
        $this->assertEquals(null, $audio->getCategoryId());

        $audio->setCategory(new Group(1));
        $audio->save();

        $audio = new Audio($id);

        $this->assertEquals(1, $audio->getCategoryId());
        $this->assertEquals(1, $audio->getCategory()->getID());

        $audio->delete();
        $this->assertNull($audio->getID());
        $audio = new Audio ();
        $this->assertNull($audio->getID());
    }

    public function testAudioHtml() {
        $audio = new Audio ();
        $audio->setName("My Name");
        $audio->setMP3File("music.mp3");
        $audio->setOGGFile("music.ogg");
        $audio->setCategoryId(1);
        $this->assertEquals(
                '<audio controls><source src="content/audio/music.mp3" type="audio/mp3"><source src="content/audio/music.ogg" type="audio/ogg">Your browser doesn\'t support HTML 5.<br/><a href="content/audio/music.mp3">But you can download the audio file here.</a></audio>', $audio->getHtml());
    }

}
