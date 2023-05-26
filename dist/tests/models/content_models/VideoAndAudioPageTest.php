<?php

use App\Models\Media\Audio;
use App\Models\Media\Video;

class VideoAndAudioPageTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::deleteFrom('content', "slug='audio_page_test' or slug='video_page_test'");
    }

    public function testSetAndGetVideo(): void {
        $video = new Video();
        $video->setName('My Name');
        $video->setMP4File('video.mp4');
        $video->setOGGFile('video.ogv');
        $video->setWebmFile('video.webm');
        $video->setCategoryId(1);
        $video->setWidth(640);
        $video->setHeight(480);
        $video->save();

        $userManager = new UserManager();
        $userId = $userManager->getAllUsers()[0]->getId();
        $groupId = Group::getAll()[0]->getId();

        $page = new Video_Page();
        $page->slug = 'video_page_test';
        $page->title = 'Video Page Test';
        $page->language = 'en';
        $page->author_id = $userId;
        $page->group_id = $groupId;
        $this->assertNull($page->getVideo());

        $page->setVideo($video);
        $this->assertEquals($video->getID(), $page->video);
        $this->assertEquals($video->getID(), $page->getVideo()->getId());

        $page->save();

        $savedPage = ContentFactory::getByID($page->getId());
        $this->assertEquals($video->getID(), $savedPage->video);

        $savedPage->setVideo(null);
        $savedPage->save();

        $savedPage->delete(false);
        $page->delete();
    }

    public function testSetAndGetAudio(): void {
        $audio = new Audio();
        $audio->setName('My Name');
        $audio->setMP3File('audio.mp3');
        $audio->setCategoryId(1);
        $audio->save();

        $userManager = new UserManager();
        $userId = $userManager->getAllUsers()[0]->getId();
        $groupId = Group::getAll()[0]->getId();

        $page = new Audio_Page();
        $page->slug = 'audio_page_test';
        $page->title = 'Audio Page Test';
        $page->language = 'en';
        $page->author_id = $userId;
        $page->group_id = $groupId;
        $this->assertNull($page->getAudio());

        $page->setAudio($audio);

        $this->assertEquals($audio->getID(), $page->audio);
        $this->assertEquals($audio->getID(), $page->getAudio()->getId());

        $page->save();

        $savedPage = ContentFactory::getByID($page->getId());
        $this->assertEquals($audio->getID(), $savedPage->audio);

        $savedPage->setAudio(null);
        $savedPage->save();

        $savedPage->delete(false);
        $page->delete();
    }
}
