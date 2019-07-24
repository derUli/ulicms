<?php

use UliCMS\Models\Media\Video;
use UliCMS\Models\Content\Category;

class VideoTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        require_once getLanguageFilePath("en");
    }

    public function testCreateUpdateAndDelete() {
        $video = new Video();
        $video->setName("My Name");
        $video->setMP4File("video.mp4");
        $video->setOGGFile("video.ogv");
        $video->setWebmFile("video.webm");
        $video->setCategoryId(1);
        $video->save();
        $this->assertNotNull($video->getID());
        $id = $video->getId();
        $video = new Video($id);
        $this->assertNotNull($video->getID());
        $this->assertEquals("My Name", $video->getName());
        $this->assertEquals("video.mp4", $video->getMP4File());
        $this->assertEquals("video.ogv", $video->getOggFile());
        $this->assertEquals("video.webm", $video->getWebmFile());
        $this->assertEquals(1, $video->getCategoryId());
        $this->assertEquals(1, $video->getCategory()
                        ->getID());

        $video->setName("New Name");
        $video->setMP4File("not-video.mp4");
        $video->setOGGFile("not-video.ogg");
        $video->setWebmFile("not-video.webm");
        $video->setCategoryId(null);
        $video->save();
        $video = new Video($id);

        $this->assertEquals("New Name", $video->getName());
        $this->assertEquals("not-video.mp4", $video->getMP4File());
        $this->assertEquals("not-video.ogg", $video->getOggFile());
        $this->assertEquals("not-video.webm", $video->getWebmFile());
        $this->assertEquals(null, $video->getCategoryId());

        $video = new Video($id);

        $video->setCategory(new Category(1));
        $video->save();

        $video = new Video($id);

        $this->assertEquals(1, $video->getCategoryId());
        $this->assertEquals(1, $video->getCategory()
                        ->getID());

        $video->delete();
        $this->assertNull($video->getID());
        $video = new Video();
        $this->assertNull($video->getID());
    }

    public function testVideoHtml() {
        $video = new Video();
        $video->setName("My Name");
        $video->setMP4File("video.mp4");
        $video->setOGGFile("video.ogv");
        $video->setWebmFile("video.webm");
        $video->setCategoryId(1);
        $this->assertEquals('<video width="" height="" controls><source src="content/videos/video.mp4" type="video/mp4"><source src="content/videos/video.ogv" type="video/ogg"><source src="content/videos/video.webm" type="video/webm">Your browser doesn\'t support HTML 5.<br/><a href="content/videos/video.mp4">But you can download the video here.</a></video>', $video->render());
    }

    public function testGetAll() {
        $savedVideos = [];
        for ($i = 1; $i <= 10; $i++) {
            $video = new Video();
            $video->setName("My Name $i");
            $video->setMP4File("video.mp4");
            $video->setOGGFile("video.ogv");
            $video->setWebmFile("video.webm");
            $video->setCategoryId(1);
            $video->save();
            $savedVideos[] = $video;
        }

        $videos = Video::getAll();
        $this->assertGreaterThanOrEqual(10, count($videos));

        foreach ($videos as $video) {
            $this->assertInstanceOf(Video::class, $video);
            $this->assertGreaterThanOrEqual(1, $video->getID());
        }

        foreach ($savedVideos as $video) {
            $video->delete();
        }
    }

}
