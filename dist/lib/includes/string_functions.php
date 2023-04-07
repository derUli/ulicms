<?php

declare(strict_types=1);

use App\Helpers\StringHelper;
use Nette\Utils\Random;

if (!defined("RESPONSIVE_FM")) {
    function sanitize(array & $array): void
    {
        foreach ($array as & $data) {
            $data = str_ireplace(array(
                "\r",
                "\n",
                "%0a",
                "%0d"
                    ), '', stripslashes($data));
        }
    }
}

function _unesc(string $string): string
{
    return html_entity_decode($string, ENT_COMPAT, "UTF-8");
}

function unesc(string $string): void
{
    echo _unesc($string);
}

/**
 * Normalize line breaks
 * @param string $txt
 * @param string $style
 * @return string
 */
function normalizeLN(string $txt, string $style = "\r\n"): string
{
    $txt = str_replace("\r\n", "\n", $txt);
    $txt = str_replace(
        "\r",
        "\n",
        $txt
    );
    $txt = str_replace("\n", $style, $txt);
    return $txt;
}

/**
 * Convert URLS in a string to HTML links
 * @param string $text
 * @return string
 */
function make_links_clickable(string $text): string
{
    return StringHelper::makeLinksClickable($text);
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
function getExcerpt(
    string $str,
    int $startPos = 0,
    int $maxLength = 100
): string {
    return StringHelper::getExcerpt($str, $startPos, $maxLength);
}

function stringOrNull($val): ?string
{
    return is_string($val) && !empty($val) ? $val : null;
}

// Aus einer Boolean einen String machen ("true" oder "false")

function strbool($value): string
{
    return($value) ? 'true' : 'false';
}

function convertLineEndingsToLN(string $s): string
{
    return normalizeLN($s, "\n");
}

function esc($value): void
{
    Template::escape($value);
}

function _esc($value): string
{
    return Template::getEscape($value);
}

function remove_prefix(string $text, string $prefix): string
{
    if (str_starts_with($text, $prefix)) {
        $text = substr($text, strlen($prefix));
    }
    return $text;
}

function remove_suffix(string $text, string $suffix): string
{
    if (str_ends_with($text, $suffix)) {
        $text = substr($text, 0, strlen($text) - strlen($suffix));
    }
    return $text;
}

function bool2YesNo(
    bool $value,
    ?string $yesString = null,
    ?string $noString = null
): string {
    if (!$yesString) {
        $yesString = get_translation("yes");
    }
    if (!$noString) {
        $noString = get_translation("no");
    }
    return ($value ? $yesString : $noString);
}

/**
 * Generate random string
 * @param int $length
 * @return string
 */
function rand_string(int $length): string
{
    return Random::generate($length);
}

function getStringLengthInBytes(string $data): int
{
    return ini_get('mbstring.func_overload') ?
            mb_strlen($data, '8bit') : strlen($data);
}

function splitAndTrim(string $str): array
{
    return array_map('trim', explode(";", $str));
}
