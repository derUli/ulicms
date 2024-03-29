<?php

use App\Models\Media\Audio;
use App\Models\Media\Video;

class HTML5MediaTest extends \PHPUnit\Framework\TestCase {
    public function testReplaceAudioTagsWithShortCode(): void {
        $audio = new Audio();
        $audio->setName('New Name');
        $audio->setMP3File('not-music.mp3');
        $audio->setOGGFile('not-music.ogg');
        $audio->setCategoryId(null);
        $audio->save();

        $this->assertEquals(
            "Foo {$audio->render()} Bar",
            replaceAudioTags(
                "Foo [audio id={$audio->getId()}] Bar"
            )
        );

        $this->assertEquals(
            "Foo {$audio->render()} Bar",
            replaceAudioTags(
                "Foo [audio id=\"{$audio->getId()}\"] Bar"
            )
        );

        $this->assertEquals(
            "Foo {$audio->render()} Bar",
            replaceAudioTags(
                "Foo [audio id=&quot;{$audio->getId()}&quot;] Bar"
            )
        );

        $audio->delete();
    }

    public function testReplaceAudioTagsWithNonExistingId(): void {
        $intMax = PHP_INT_MAX;
        $this->assertEquals(
            "Foo [audio id={$intMax}] Bar",
            replaceAudioTags(
                "Foo [audio id={$intMax}] Bar"
            )
        );

        $this->assertEquals(
            "Foo [audio id=\"{$intMax}\"] Bar",
            replaceAudioTags(
                "Foo [audio id=\"{$intMax}\"] Bar"
            )
        );
        $this->assertEquals(
            "Foo [audio id=&quot;{$intMax}]&quot; Bar",
            replaceAudioTags(
                "Foo [audio id=&quot;{$intMax}]&quot; Bar"
            )
        );
    }

    public function testReplaceAudioTagsWithoutShortCode(): void {
        $this->assertEquals(
            'Foo Hello World Bar',
            replaceAudioTags(
                'Foo Hello World Bar'
            )
        );
    }

    public function testReplaceVideoTagsWithShortCode(): void {
        $video = new Video();
        $video->setName('My Name');
        $video->setMP4File('video.mp4');
        $video->setOGGFile('video.ogv');
        $video->setWebmFile('video.webm');
        $video->setWidth(640);
        $video->setHeight(480);
        $video->setCategoryId(1);
        $video->save();

        $this->assertEquals(
            "Foo {$video->render()} Bar",
            replaceVideoTags(
                "Foo [video id={$video->getId()}] Bar"
            )
        );

        $this->assertEquals(
            "Foo {$video->render()} Bar",
            replaceVideoTags(
                "Foo [video id=\"{$video->getId()}\"] Bar"
            )
        );

        $this->assertEquals(
            "Foo {$video->render()} Bar",
            replaceVideoTags(
                "Foo [video id=&quot;{$video->getId()}&quot;] Bar"
            )
        );

        $video->delete();
    }

    public function testReplaceVideoTagsWithNonExistingId(): void {
        $intMax = PHP_INT_MAX;
        $this->assertEquals(
            "Foo [video id={$intMax}] Bar",
            replaceVideoTags(
                "Foo [video id={$intMax}] Bar"
            )
        );

        $this->assertEquals(
            "Foo [video id=\"{$intMax}\"] Bar",
            replaceVideoTags(
                "Foo [video id=\"{$intMax}\"] Bar"
            )
        );
        $this->assertEquals(
            "Foo [video id=&quot;{$intMax}]&quot; Bar",
            replaceVideoTags(
                "Foo [video id=&quot;{$intMax}]&quot; Bar"
            )
        );
    }

    public function testReplaceVideoTagsWithoutShortCode(): void {
        $this->assertEquals(
            'Foo Hello World Bar',
            replaceVideoTags(
                'Foo Hello World Bar'
            )
        );
    }
}
