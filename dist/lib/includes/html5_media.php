<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Models\Media\Audio;
use App\Models\Media\Video;

/**
 * Replaces audio tags with rendered html5 audio elements
 * @param string $txt
 *
 * @return string
 */
function replaceAudioTags(string $txt): string {
    $audio_dir = 'content/audio/';

    // TODO: Use Regex
    $contains = str_contains($txt, '[audio id=')  ;

    // If there is no [audio=XXX] in page return
    if (! $contains) {
        return $txt;
    }

    $audios = Audio::getAll();

    foreach ($audios as $audio) {
        $code1 = '[audio id="' . $audio->getId() . '"]';
        $code2 = '[audio id=&quot;' . $audio->getId() . '&quot;]';
        $code3 = '[audio id=' . $audio->getId() . ']';

        $html = $audio->render();

        $txt = str_replace($code1, $html, $txt);
        $txt = str_replace($code2, $html, $txt);
        $txt = str_replace($code3, $html, $txt);
    }

    return $txt;
}

/**
 * Replaces video tags with html5 video elements
 * @param string $txt
 * @return string
 */
function replaceVideoTags(string $txt): string {
    $video_dir = 'content/videos/';

    // TODO: Use Regex
    $contains = str_contains($txt, '[video id=')  ;

    // If there is no [video=XXX] in page return
    if (! $contains) {
        return $txt;
    }

    $videos = Video::getAll();

    foreach ($videos as $video) {
        $code1 = '[video id="' . $video->getId() . '"]';
        $code2 = '[video id=&quot;' . $video->getId() . '&quot;]';
        $code3 = '[video id=' . $video->getId() . ']';

        $html = $video->render();

        $txt = str_replace($code1, $html, $txt);
        $txt = str_replace($code2, $html, $txt);
        $txt = str_replace($code3, $html, $txt);
    }

    return $txt;
}
