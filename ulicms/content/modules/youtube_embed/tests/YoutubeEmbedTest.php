<?php

use PHPUnit\Framework\TestCase;

class YoutubeEmbedTest extends TestCase {

    // youtube.com/watch?v=xyz
    public function testGetVideoIdFromFullYoutubeUrl() {
        $videoUrl = "https://www.youtube.com/watch?v=d6sfgRTEzjI";
        $controller = ModuleHelper::getMainController("youtube_embed");
        $this->assertEquals("d6sfgRTEzjI", $controller->getVideoId($videoUrl));
    }

    // youtu.be/xyz
    public function testGetVideoIdFromShortYoutubeUrl() {
        $videoUrl = "https://youtu.be/qyqWGSmo9PY";
        $controller = ModuleHelper::getMainController("youtube_embed");
        $this->assertEquals("qyqWGSmo9PY", $controller->getVideoId($videoUrl));
    }

    // Invalid url returns null
    public function testGetVideoIdFromInvalidUrl() {
        $videoUrl = "http://this-url-is-crap.com/?a=213213123123";
        $controller = ModuleHelper::getMainController("youtube_embed");
        $this->assertNull($controller->getVideoId($videoUrl));
    }

}
