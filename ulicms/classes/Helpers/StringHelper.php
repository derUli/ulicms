<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use Cocur\Slugify\Slugify;

class StringHelper extends Helper {

    // removes empty lines from a string
    public static function removeEmptyLinesFromString(string $input): string {
        return normalizeLN(
                preg_replace(
                        "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/",
                        "\n",
                        $input
                ),
                "\n"
        );
    }

    // returns true if the string is null or empty
    public static function isNullOrEmpty($variable): bool {
        return (is_null($variable) or empty($variable));
    }

    // returns true if the string is not null or empty
    public static function isNotNullOrEmpty($variable): bool {
        return (!is_null($variable) && !empty($variable));
    }

    // returns true if the string is null or whitespace
    public static function isNullOrWhitespace(?string $variable): bool {
        return $variable ? self::isNullOrEmpty(trim($variable)) : true;
    }

    // returns true if the string is not null or whitespace
    public static function isNotNullOrWhitespace($variable): bool {
        return $variable ? self::isNotNullOrEmpty(trim($variable)) : false;
    }

    // clean a string to use it in urls
    public static function cleanString(
            string $string,
            string $separator = '-'
    ): string {
        $slugify = new Slugify();
        return $slugify->slugify($string, $separator);
    }

    /**
     * Encode strings to prevent XSS
     * @param string $string Input
     * @return string Output
     */
    public static function realHtmlSpecialchars(
            string $string
    ): string {
        return _esc($string);
    }

    /**
     * Replace urls with clickable html links
     * @param string $text Input
     * @return string Output
     */
    public static function makeLinksClickable(string $text): string {
        return preg_replace(
                '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i',
                '<a href="$1" rel="nofollow" target="_blank">$1</a>',
                $text
        );
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
    public static function getExcerpt(
            string $str,
            int $startPos = 0,
            int $maxLength = 100
    ): string {
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

    /**
     * Returns true if the string is empty
     * @param type $str
     * @return bool
     */
    public static function isEmpty($str): bool {
        $str = trim($str);
        return empty($str);
    }

    /**
     * Decode html entities
     * @param string $str Input
     * @return string Output
     */
    public static function decodeHTMLEntities(string $str): string {
        return html_entity_decode($str, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Get most common words
     * @param string $text Input
     * @return array Words
     */
    public static function keywordsFromString(string $text): array {
        $text = normalizeLN($text, "\n");
        $words = [];

        // Punkt, Beistrich, Zeilenumbruch... in Leerzeichen umwandeln
        $text = str_replace(array(
            "\n",
            ".",
            ","
                ), " ", $text);

        // text an Leerzeichen zerlegen
        $textWords = explode(" ", $text);
        $textWords = array_filter($textWords);

        foreach ($textWords as $word) {
            if (!array_key_exists($word, $words)) {
                // wenn das wort zum ersten mal gefunden wurde
                $words[$word] = 1;
            } else {
                // wenn schon vorhanden
                $words[$word]++;
            }
        }

        $words = array_filter($words, "decodeHTMLEntities");

        // nach häufigkeit sortieren
        arsort($words);

        // array zurückgeben
        return $words;
    }

    // converts a string to an array of lines
    public static function linesFromString(
            string $str,
            bool $trim = true,
            bool $removeEmpty = true,
            bool $removeComments = true
    ): array {
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

    // Reads a file and converts it to an array of lines
    public static function linesFromFile(
            string $file,
            bool $trim = true,
            bool $removeEmpty = true,
            bool $removeComments = true
    ): ?array {
        $lines = null;
        if (file_exists($file)) {
            $str = file_get_contents($file);
            $lines = self::linesFromString(
                            $str,
                            $trim,
                            $removeEmpty,
                            $removeComments
            );
        }
        return $lines;
    }

    /**
     * Trims all lines of string
     * @param string $str input string
     * @param string $newline Line Breaks
     * @return string processed string
     */
    public static function trimLines(
            string $str,
            string $newline = PHP_EOL
    ): string {
        $str = StringHelper::linesFromString($str, true, true, false);
        $str = implode($newline, $str);
        return $str;
    }

    /**
     * Check if a string is all upper case
     * @param string $val input string
     * @return bool result
     */
    public static function isUpperCase(string $val): bool {
        return strtoupper($val) === $val;
    }

    /**
     * Check if a string is all lower case
     * @param string $val input string
     * @return bool result
     */
    public static function isLowerCase(string $val): bool {
        return strtolower($val) === $val;
    }

}
