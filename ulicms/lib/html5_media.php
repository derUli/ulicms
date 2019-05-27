<?php

function replaceAudioTags($txt) {
    $audio_dir = "content/audio/";
    if (defined("ULICMS_DATA_STORAGE_URL")) {
        $audio_dir = Path::resolve("ULICMS_DATA_STORAGE_URL/$audio_dir") . "/";
    }
    // Ich weiß, dass das eigentlich einfacher mit einem regulären Ausdruck geht, aber ich kann keine reguläre Ausdrücke.
    // Reguläre Ausdrücke sehen für mich so aus, als wäre eine Katze über die Tastatur gelaufen.
    $contains = strpos($txt, "[audio id=") !== FALSE;

    if ($contains) {
        $query = db_query("select id, ogg_file, mp3_file from " . tbname("audio") . " order by id");

        while ($row = db_fetch_object($query)) {
            $code1 = "[audio id=\"" . $row->id . "\"]";
            $code2 = "[audio id=$quot;" . $row->id . "$quot;]";
            $code3 = "[audio id=" . $row->id . "]";
            if (!empty($row->mp3_file)) {
                $preferred = $row->mp3_file;
            } else {
                $preferred = $row->ogg_file;
            }

            $html = '<audio controls>';
            if (!empty($row->mp3_file)) {
                $html .= '<source src="' . $audio_dir . _esc($row->mp3_file) . '" type="audio/mp3">';
            }
            if (!empty($row->ogg_file)) {
                $html .= '<source src="' . $audio_dir . _esc($row->ogg_file) . '" type="audio/ogg">';
            }
            $html .= get_translation("no_html5");
            if (!empty($row->mp3_file) or ! empty($row->ogg_file)) {
                $html .= '<br/>
		<a href="' . $audio_dir . $preferred . '">' . TRANSLATION_DOWNLOAD_AUDIO_INSTEAD . '</a>';
            }
            $html .= '</audio>';
            $txt = str_replace($code1, $html, $txt);
            $txt = str_replace($code2, $html, $txt);
            $txt = str_replace($code3, $html, $txt);
        }
    }
    return $txt;
}

function replaceVideoTags($txt) {
    $video_dir = "content/videos/";
    if (defined("ULICMS_DATA_STORAGE_URL")) {
        $video_dir = Path::resolve("ULICMS_DATA_STORAGE_URL/$video_dir") . "/";
    }

    // Ich weiß, dass das eigentlich einfacher mit einem regulären Ausdruck geht, aber ich kann keine reguläre Ausdrücke.
    // Reguläre Ausdrücke sehen für mich so aus, als wäre eine Katze über die Tastatur gelaufen.
    $contains = strpos($txt, "[video id=") !== FALSE;

    if ($contains) {
        $query = db_query("select id, ogg_file, webm_file, mp4_file, width, height from " . tbname("videos") . " order by id");

        while ($row = db_fetch_object($query)) {
            $code1 = "[video id=\"" . $row->id . "\"]";
            $code2 = "[video id=$quot;" . $row->id . "$quot;]";
            $code3 = "[video id=" . $row->id . "]";
            if (!empty($row->mp4_file)) {
                $preferred = $row->mp4_file;
            } else if (!empty($row->ogg_file)) {
                $preferred = $row->ogg_file;
            } else {
                $preferred = $row->webm_file;
            }

            $html = '<video width="' . $row->width . '" height="' . $row->height . '" controls>';
            if (!empty($row->mp4_file)) {
                $html .= '<source src="' . $video_dir . _esc($row->mp4_file) . '" type="video/mp4">';
            }
            if (!empty($row->ogg_file)) {
                $html .= '<source src="' . $video_dir . _esc($row->ogg_file) . '" type="video/ogg">';
            }
            if (!empty($row->webm_file)) {
                $html .= '<source src="' . $video_dir . _esc($row->webm_file) . '" type="video/webm">';
            }
            $html .= TRANSLATION_NO_HTML5;
            if (!empty($row->mp4_file) or ! empty($row->ogg_file) or ! empty($row->webm_file)) {
                $html .= '<br/>
		<a href="' . $video_dir . $preferred . '">' . TRANSLATION_DOWNLOAD_VIDEO_INSTEAD . '</a>';
            }
            $html .= '</video>';
            $txt = str_replace($code1, $html, $txt);
            $txt = str_replace($code2, $html, $txt);
            $txt = str_replace($code3, $html, $txt);
        }
    }
    return $txt;
}
