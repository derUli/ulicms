<?php

use UliCMS\Models\Media\Audio;
use UliCMS\Models\Content\Categories;

class AudioControllerTest extends \PHPUnit\Framework\TestCase {

    protected function tearDown(): void {
        $_POST = [];
        Database::deleteFrom("audio", "name like 'test-audio-%'", true);
    }

    public function testUpdatePostReturnsTrue(): void {
        $categories = Categories::getAllCategories();
        $first = $categories[0];

        $audio = new Audio();
        $audio->setName("test-audio-1");
        $audio->setMP3File("test-audio-1.mp3");
        $audio->setOGGFile("test-audio-1.ogg");
        $audio->setCategory($first);
        $audio->save();


        $_POST = [
            "name" => "test-audio-2",
            "mp3_file" => "test-audio-2.mp3",
            "ogg_file" => "test-audio-2.ogg",
            "id" => $audio->getID(),
            "category_id" => $first->getId()
        ];

        $controller = new AudioController();
        $this->assertTrue($controller->_updatePost());

        $audio->reload();

        $this->assertEquals("test-audio-2", $audio->getName());
        $this->assertEquals("test-audio-2.mp3", $audio->getMP3File());
        $this->assertEquals("test-audio-2.ogg", $audio->getOggFile());
        $this->assertEquals($first->getId(), $audio->getCategoryId());
    }

    public function testUpdatePostReturnsFalse(): void {
        $_POST = [
            "name" => "test-audio-2",
            "mp3_file" => "test-audio-2.mp3",
            "ogg_file" => "test-audio-2.ogg",
            "id" => PHP_INT_MAX
        ];

        $controller = new AudioController();
        $this->assertFalse($controller->_updatePost());
    }

}
