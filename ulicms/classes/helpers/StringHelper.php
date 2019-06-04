<?php

use Cocur\Slugify\Slugify;

class StringHelper extends Helper {

    public static function removeEmptyLinesFromString($input) {
        return normalizeLN(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $input), "\n");
    }

    public static function isNullOrEmpty($variable) {
        return (is_null($variable) or empty($variable));
    }

    public static function isNotNullOrEmpty($variable) {
        return (!is_null($variable) and ! empty($variable));
    }

    public static function isNullOrWhitespace($variable) {
        return self::isNullOrEmpty(trim($variable));
    }

    public static function isNotNullOrWhitespace($variable) {
        return self::isNotNullOrEmpty(trim($variable));
    }

    public static function cleanString($string, $separator = '-') {
        $slugify = new Slugify();
        return $slugify->slugify($string, $separator);
    }

    public static function realHtmlSpecialchars($string) {
        return _esc($string);
    }

    // Links klickbar machen
    public static function makeLinksClickable($text) {
        return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" rel="nofollow" target="_blank">$1</a>', $text);
    }

    /**
     * Get excerpt from string
     *
     * @param String $str
     *            String to get an excerpt from
     * @param Integer $startPos
     *            Position int string to start excerpt from
     * @param Integer $maxLength
     *            Maximum length the excerpt may be
     * @return String excerpt
     */
    public static function getExcerpt($str, $startPos = 0, $maxLength = 100) {
        $str = str_replace("&nbsp;", " ", $str);
        if (strlen($str) > $maxLength) {
            $excerpt = substr($str, $startPos, $maxLength - 3);
            $lastSpace = strrpos($excerpt, ' ');
            $excerpt = substr($excerpt, 0, $lastSpace);
            $excerpt .= '...';
        } else {
            $excerpt = $str;
        }

        return $excerpt;
    }

    public static function isEmpty($str) {
        $str = trim($str);
        return empty($str);
    }

    public static function decodeHTMLEntities($str) {
        return html_entity_decode($str, ENT_COMPAT, 'UTF-8');
    }

    // Häufigste Wörter in String ermitteln und als Assoziatives Array zurückgeben.
    // z.B. für automatisches ausfüllen der Meta-Keywords nutzbar
    public static function keywordsFromString($text) {
        $return = [];

        // Punkt, Beistrich, Zeilenumbruch... in Leerzeichen umwandeln
        $text = str_replace(array(
            "\n",
            ".",
            ","
                ), " ", $text);

        // text an Leerzeichen zerlegen
        $array = explode(" ", $text);

        foreach ($array as $word) {
            if (strlen($word) == 0) {
                // wenn kein Wort vorhanden ist nichts machen
                continue;
            }
            if (!faster_in_array($word, $array)) {
                // wenn das wort zum ersten mal gefunden wurde
                $return[$word] = 1;
            } else {
                // wenn schon vorhanden
                $return[$word] ++;
            }
        }

        $return = array_filter($return, "decodeHTMLEntities");
        // nach häufigkeit sortieren
        arsort($return);

        // array zurückgeben
        return $return;
    }

    public static function linesFromString($str, $trim = true, $removeEmpty = true, $removeComments = true) {
        $str = normalizeLN($str, "\n");
        $lines = explode("\n", $str);
        if ($trim) {
            $lines = array_map("trim", $lines);
        }
        if ($removeEmpty) {
            $lines = array_filter($lines, "strlen");
        }
        if ($removeComments) {
            $lines = array_filter($lines, function ($line) {
                return !startsWith($line, "#");
            });
        }
        $lines = array_values($lines);
        return $lines;
    }

    public static function linesFromFile($file, $trim = true, $removeEmpty = true, $removeComments = true) {
        $lines = null;
        if (is_File($file)) {
            $str = file_get_contents($file);
            $lines = self::linesFromString($str, $trim, $removeEmpty, $removeComments);
        }
        return $lines;
    }

    public static function trimLines($str, $newline = PHP_EOL) {
        $str = StringHelper::linesFromString($str, true, true, false);
        $str = implode($newline, $str);
        return $str;
    }

}
