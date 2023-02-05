<?php

declare(strict_types=1);

use UliCMS\Models\Media\Audio;
use UliCMS\Models\Media\Video;

/**
 * Replace all [audio id=xxx] tags in a HTML string with HTML5 <audio> Tags
 * @param string $txt HTML String
 * @return string|null HTML with replaced audio tags
 */
function replaceAudioTags(string $txt): ?string {
    $audio_dir = "content/audio/";

    $matches = [];
    preg_match_all('/\[audio id=(?:\D+)?(\d+)(?:\D+)?\]/im', $txt, $matches);

    $embedTagIds = [];

    for ($i = 0; $i < count($matches[0]); $i++) {
        $embedTagIds[$matches[0][$i]] = (int) $matches[1][$i];
    }

    foreach ($embedTagIds as $embedTag => $id) {
        $audio = new Audio($id);

        if ($audio->isPersistent()) {
            $htmlCode = $audio->render();

            $txt = str_replace($embedTag, $htmlCode, $txt);
        }
    }

    return $txt;
}

/**
 * Replace all [video id=xxx] tags in a HTML string with HTML5 <video> Tags
 * @param string $txt HTML String
 * @return string|null HTML with replaced video Tags
 */
function replaceVideoTags(string $txt): string {
    $video_dir = "content/videos/";

    $matches = [];
    preg_match_all('/\[video id=(?:\D+)?(\d+)(?:\D+)?\]/im', $txt, $matches);

    $embedTagIds = [];

    for ($i = 0; $i < count($matches[0]); $i++) {
        $embedTagIds[$matches[0][$i]] = (int) $matches[1][$i];
    }

    foreach ($embedTagIds as $embedTag => $id) {
        $video = new Video($id);

        if ($video->isPersistent()) {
            $htmlCode = $video->render();

            $txt = str_replace($embedTag, $htmlCode, $txt);
        }
    }

    return $txt;
}
