<?php

declare(strict_types=1);

use UliCMS\Models\Media\Audio;
use UliCMS\Models\Media\Video;

function replaceAudioTags(string $txt): ?string {
    $audio_dir = "content/audio/";
    if (defined("ULICMS_DATA_STORAGE_URL")) {
        $audio_dir = Path::resolve("ULICMS_DATA_STORAGE_URL/$audio_dir") . "/";
    }
    // Ich weiß, dass das eigentlich einfacher mit einem
    // regulären Ausdruck geht, aber ich kann keine reguläre Ausdrücke.
    // Reguläre Ausdrücke sehen für mich so aus, als wäre
    // eine Katze über die Tastatur gelaufen.
    $contains = strpos($txt, "[audio id=") !== FALSE;

    if (!$contains) {
        return $txt;
    }
    $audios = Audio::getAll();

    foreach ($audios as $audio) {
        $code1 = "[audio id=\"" . $audio->getId() . "\"]";
        $code2 = "[audio id=&quot;" . $audio->getId() . "&quot;]";
        $code3 = "[audio id=" . $audio->getId() . "]";

        $html = $audio->render();

        $txt = str_replace($code1, $html, $txt);
        $txt = str_replace($code2, $html, $txt);
        $txt = str_replace($code3, $html, $txt);
    }

    return $txt;
}

function replaceVideoTags(string $txt): string {
    $video_dir = "content/videos/";
    if (defined("ULICMS_DATA_STORAGE_URL")) {
        $video_dir = Path::resolve("ULICMS_DATA_STORAGE_URL/$video_dir") . "/";
    }

    // Ich weiß, dass das eigentlich einfacher mit einem
    // regulären Ausdruck geht, aber ich kann keine reguläre Ausdrücke.
    // Reguläre Ausdrücke sehen für mich so aus, als wäre
    // eine Katze über die Tastatur gelaufen.
    $contains = strpos($txt, "[video id=") !== false;

    if (!$contains) {
        return $txt;
    }

    $videos = Video::getAll();

    foreach ($videos as $video) {
        $code1 = "[video id=\"" . $video->getId() . "\"]";
        $code2 = "[video id=&quot;" . $video->getId() . "&quot;]";
        $code3 = "[video id=" . $video->getId() . "]";

        $html = $video->render();

        $txt = str_replace($code1, $html, $txt);
        $txt = str_replace($code2, $html, $txt);
        $txt = str_replace($code3, $html, $txt);
    }

    return $txt;
}
