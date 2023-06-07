<?php

declare(strict_types=1);

namespace App\Helpers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Nette\Utils\Strings;

abstract class StringHelper extends Helper {
    // removes empty lines from a string
    public static function removeEmptyLinesFromString(string $input): string {
        return normalizeLN(
            preg_replace(
                "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/",
                "\n",
                $input
            ) ?? '',
            "\n"
        );
    }

    // clean a string to use it in urls
    public static function cleanString(
        string $string
    ): string {
        return Strings::webalize($string);
    }

    /**
     * Replace urls with clickable HTML links
     *
     * @param string $text
     *
     * @return string
     */
    public static function makeLinksClickable(string $text): string {
        return preg_replace(
            '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i',
            '<a href="$1" rel="nofollow" target="_blank">$1</a>',
            $text
        ) ?? '';
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
        $str = str_replace('&nbsp;', ' ', $str);
        if (strlen($str) > $maxLength) {
            $excerpt = substr($str, $startPos, $maxLength - 3);
            $lastSpace = strrpos($excerpt, ' ');
            $excerpt = substr($excerpt, 0, (int)$lastSpace);
            $excerpt .= '...';
        }

        return $excerpt;
    }

    /**
     * Converts a string to an array of lines
     *
     * @param string $str
     * @param bool $trim
     * @param bool $removeEmpty
     * @param bool $removeComments
     *
     * @return string[]
     */
    public static function linesFromString(
        string $str,
        bool $trim = true,
        bool $removeEmpty = true,
        bool $removeComments = true
    ): array {
        $str = normalizeLN($str, "\n");
        $lines = explode("\n", $str);

        if ($trim) {
            $lines = array_map('trim', $lines);
        }

        if ($removeEmpty) {
            $lines = array_filter($lines, 'strlen');
        }

        if ($removeComments) {
            $lines = array_filter($lines, static function($line) {
                return ! str_starts_with($line, '#');
            });
        }

        $lines = array_values($lines);
        return $lines;
    }

    /**
     * Reads a file and converts it to an array of lines
     *
     * @param string $file
     * @param bool $trim
     * @param bool $removeEmpty
     * @param bool $removeComments
     *
     * @return string[]
     */
    public static function linesFromFile(
        string $file,
        bool $trim = true,
        bool $removeEmpty = true,
        bool $removeComments = true
    ): ?array {
        $lines = null;

        if (is_file($file)) {
            $str = (string)file_get_contents($file);
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
     * Trim lines of multiline string
     *
     * @param string $str
     * @param $newline
     *
     * @return string
     */
    public static function trimLines(
        string $str,
        string $newline = PHP_EOL
    ): string {
        $str = \App\Helpers\StringHelper::linesFromString($str, true, true, false);
        $str = implode($newline, $str);
        return $str;
    }

    /**
     * Split and trim a semicolon separated string
     * string $str
     *
     * @param string $str
     *
     * @return array<string>
     */
    public static function splitAndTrim(string $str): array {
        return array_map('trim', explode(';', $str));
    }
}
