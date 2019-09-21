<?php

use UliCMS\Models\Media\Audio;
use UliCMS\Models\Content\Category;

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

        sleep(1);

        $this->assertGreaterThanOrEqual(time() - 5, $audio->getCreated());
        $this->assertEquals($audio->getCreated(), $audio->getUpdated());

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

        $audio->setCategory(new Category(1));
        $audio->save();


        $this->assertNotEquals($audio->getCreated(), $audio->getUpdated());

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
                '<audio controls><source src="content/audio/music.mp3" type="audio/mp3"><source src="content/audio/music.ogg" type="audio/ogg">Your browser doesn\'t support HTML 5.<br/><a href="content/audio/music.mp3">But you can download the audio file here.</a></audio>', $audio->render());
    }

    public function testGetAll() {
        $savedAudios = [];

        for ($i = 1; $i <= 10; $i++) {
            $audio = new Audio ();
            $audio->setName("My Name $i");
            $audio->setMP3File("music.mp3");
            $audio->setOGGFile("music.ogg");
            $audio->setCategoryId(1);
            $audio->save();
            $savedAudios[] = $audio;
        }
        $audios = Audio::getAll();
        $this->assertGreaterThanOrEqual(10, count($audios));

        foreach ($audios as $audio) {
            $this->assertInstanceOf(Audio::class, $audio);
            $this->assertGreaterThanOrEqual(1, $audio->getID());
        }

        foreach ($savedAudios as $audio) {
            $audio->delete();
        }
    }

}
