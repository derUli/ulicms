<?php

use App\Models\Content\Categories;
use App\Models\Media\Video;

class VideoControllerTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
        Database::deleteFrom('videos', "name like 'test-video-%'", true);
    }

    public function testUpdatePostReturnsTrue(): void
    {
        $categories = Categories::getAllCategories();
        $first = $categories[0];

        $video = new Video();
        $video->setName('test-video-1');
        $video->setMp4File('test-video-1.mp4');
        $video->setOGGFile('test-video-1.ogv');
        $video->setWebmFile('test-video-1.ogv');
        $video->setWidth(512);
        $video->setHeight(384);

        $video->setCategory($first);
        $video->save();

        $_POST = [
            'name' => 'test-video-2',
            'mp4_file' => 'test-video-2.mp4',
            'ogg_file' => 'test-video-2.ogv',
            'webm_file' => 'test-video-2.webm',
            'id' => $video->getID(),
            'width' => '640',
            'height' => '480',
            'category_id' => $first->getId()
        ];

        $controller = new VideoController();
        $this->assertTrue($controller->_updatePost());

        $video->reload();

        $this->assertEquals('test-video-2', $video->getName());
        $this->assertEquals('test-video-2.mp4', $video->getMP4File());
        $this->assertEquals('test-video-2.ogv', $video->getOggFile());
        $this->assertEquals('test-video-2.webm', $video->getWebmFile());

        $this->assertEquals($first->getId(), $video->getCategoryId());
        $this->assertEquals(640, $video->getWidth());
        $this->assertEquals(480, $video->getHeight());
    }

    public function testUpdatePostReturnsFalse(): void
    {
        $categories = Categories::getAllCategories();
        $first = $categories[0];

        $_POST = [
            'name' => 'test-video-2',
            'mp4_file' => 'test-video-2.mp4',
            'ogg_file' => 'test-video-2.ogv',
            'webm_file' => 'test-video-2.webm',
            'id' => PHP_INT_MAX,
            'width' => '640',
            'height' => '480',
            'category_id' => $first->getId()
        ];

        $controller = new VideoController();
        $this->assertFalse($controller->_updatePost());
    }
}
