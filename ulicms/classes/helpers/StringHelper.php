<?php

declare(strict_types=1);

use Cocur\Slugify\Slugify;

class StringHelper extends Helper {

    public static function removeEmptyLinesFromString(string $input): string {
        return normalizeLN(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $input), "\n");
    }

    public static function isNullOrEmpty($variable): bool {
        return (is_null($variable) or empty($variable));
    }

    public static function isNotNullOrEmpty($variable): bool {
        return (!is_null($variable) and ! empty($variable));
    }

    public static function isNullOrWhitespace(?string $variable): bool {
        return $variable ? self::isNullOrEmpty(trim($variable)) : true;
    }

    public static function isNotNullOrWhitespace($variable): bool {
        return $variable ? self::isNotNullOrEmpty(trim($variable)) : false;
    }

    public static function cleanString(string $string, string $separator = '-'): string {
        $slugify = new Slugify();
        return $slugify->slugify($string, $separator);
    }

    public static function realHtmlSpecialchars(string $string): string {
        return _esc($string);
    }

    // Links klickbar machen
    public static function makeLinksClickable(string $text): string {
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
    public static function getExcerpt(string $str, int $startPos = 0, int $maxLength = 100): string {
        $excerpt = $str;
        $str = str_replace("&nbsp;", " ", $str);
        if (strlen($str) > $maxLength) {
            $excerpt = substr($str, $startPos, $maxLength - 3);
            $lastSpace = strrpos($excerpt, ' ');
            $excerpt = substr($excerpt, 0, $lastSpace);
            $excerpt .= '...';
        }

        return $excerpt;
    }

    public static function isEmpty($str): bool {
        $str = trim($str);
        return empty($str);
    }

    public static function decodeHTMLEntities(string $str): string {
        return html_entity_decode($str, ENT_COMPAT, 'UTF-8');
    }

    // Häufigste Wörter in String ermitteln und als Assoziatives Array zurückgeben.
    // z.B. für automatisches ausfüllen der Meta-Keywords nutzbar
    public static function keywordsFromString(string $text): array {
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

    public static function linesFromString(string $str, bool $trim = true, bool $removeEmpty = true, bool $removeComments = true): array {
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

    public static function linesFromFile(string $file, bool $trim = true, bool $removeEmpty = true, bool $removeComments = true): ?array {
        $lines = null;
        if (file_exists($file)) {
            $str = file_get_contents($file);
            $lines = self::linesFromString($str, $trim, $removeEmpty, $removeComments);
        }
        return $lines;
    }

    public static function trimLines(string $str, string $newline = PHP_EOL): string {
        $str = StringHelper::linesFromString($str, true, true, false);
        $str = implode($newline, $str);
        return $str;
    }

    public static function isUpperCase(string $val): bool {
        return strtoupper($val) === $val;
    }

    public static function isLowerCase(string $val): bool {
        return strtolower($val) === $val;
    }

}
