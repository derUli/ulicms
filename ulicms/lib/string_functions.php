<?php

declare(strict_types=1);

function cleanString(string $string, string $separator = '-'): string {
    return StringHelper::cleanString($string, $separator);
}

if(!defined("RESPONSIVE_FM")){
    function sanitize(array & $array): void {
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

// TODO: Deprecate this
// use unesc() and _unesc() instead
function unhtmlspecialchars(string $string): string {
    return _unesc($string);
}

function _unesc(string $string): string {
    return html_entity_decode($string, ENT_COMPAT, "UTF-8");
}

function unesc(string $string): void {
    echo _unesc($string);
}

function br2nlr(string $html
): string {
    return preg_replace('#<br\s*/?>#i', "\r\n", $html);
}

function normalizeLN(string $txt, string $style = "\r\n"): string {
    $txt = str_replace("\r\n", "\n", $txt);
    $txt = str_replace(
            "\r", "\n", $txt);
    $txt = str_replace("\n", $style, $txt);
    return $txt;
}

function real_htmlspecialchars(string $string): string {
    return StringHelper::realHtmlSpecialchars($string);
}

function multi_explode(array $delimiters, string $string): array {
    return explode($delimiters[0],
            strtr($string,
                    array_combine(array_slice($delimiters, 1),
                            array_fill(0, count($delimiters) - 1,
                                    array_shift($delimiters)))));
}

// Links klickbar machen
function make_links_clickable(string $text): string {
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

function decodeHTMLEntities(string $str): string {
    return StringHelper::decodeHTMLEntities($str);
}

// Häufigste Wörter in String ermitteln und als Assoziatives Array zurückgeben.
// z.B. für automatisches ausfüllen der Meta-Keywords nutzbar
function keywordsFromString(string $text): array {
    $return = [];

    // Punkt, Beistrich, Zeilenumbruch... in Leerzeichen umwandeln
    $text = str_replace(array(
        "\n",
        ".",
        ",",
        "!",
        "?"
            ), " ", $text);

// text an Leerzeichen zerlegen
    $array = explode(" ", $text);

    foreach ($array as $word) {
        if (strlen($word) == 0) {
// wenn kein Wort vorhanden ist nichts machen
            continue;
        }
        if (!isset($return[$word])) {
            // wenn das wort zum ersten mal gefunden wurde

            $return[$word] = 1;
        } else {
            // wenn schon vorhanden
            $return[$word] ++;
        }
    }

    $return = array_filter($return,
            "decodeHTMLEntities");
    // nach häufigkeit sortieren
    arsort($return);

    // array zurückgeben
    return $return;
}

function stringOrNull($val): ?string {
    return is_present($val) ? $val : null;
}

// Aus einer Boolean einen String machen ("true" oder "false")

function strbool($value): string {
    return($value) ? 'true' : 'false';
}

function isNullOrEmpty($variable): bool {
    trigger_error("global function isNullOrEmpty() is deprecated. "
            . "Plese use StringHelper::isNullOrEmpty() instead."
            , E_USER_WARNING);
    return is_blank($variable);
}

function isNotNullOrEmpty($variable): bool {
    trigger_error("global function isNotNullOrEmpty() is deprecated. "
            . "Plese use StringHelper::isNotNullOrEmpty() instead.", E_USER_WARNING
    );
    return is_present($variable);
}

function convertLineEndingsToLN(string $s): string {
    // Normalize line endings using Global
    // Convert all line-endings to UNIX format
    $s = str_replace(CRLF, LF, $s);
    $s = str_replace(CR, LF, $s);
// Don't allow out-of-control blank lines
    $s = preg_replace("/\n{2,}/", LF . LF, $s);
    return $s;
}

function str_replace_nth(
        string $search,
        string $replace,
        string $subject,
        int $nth): string {
    $found = preg_match_all('/' .
            preg_quote($search) . '/', $subject, $matches, PREG_OFFSET_CAPTURE);
    if (false !== $found && $found > $nth) {
        return substr_replace(
                $subject,
                $replace,
                $matches[0][$nth][1],
                strlen($search)
        );
    }
    return $subject;
}

function str_replace_first(
        string $search,
        string $replace,
        string $subject
): string {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function esc($value): void {
    Template::escape($value);
}

function _esc($value): string {
    return Template::getEscape($value);
}

function remove_prefix(string $text, string $prefix): string {
    if (startsWith($text, $prefix)) {
        $text = substr($text, strlen($prefix));
    }
    return $text;
}

function remove_suffix(string $text, string $suffix): string {
    if (endsWith($text, $suffix)) {
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

// Random string generieren (für Passwort)
function rand_string(int $length): string {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $length);
}

function getStringLengthInBytes(string $data): int {
    return ini_get('mbstring.func_overload') ?
            mb_strlen($data, '8bit') : strlen($data);
}

function splitAndTrim(string $str): array {
    return array_map('trim', explode(";", $str));
}

// this magic method replaces html num entities with the character
// used in PlainTextRenderer
function replace_num_entity($ord): string {
    $ord = $ord[1];
    if (preg_match('/^x([0-9a-f]+)$/i', $ord, $match)) {
        $ord = hexdec($match[1]);
    } else {
        $ord = intval($ord);
    }

    $no_bytes = 0;
    $byte = [];

    if ($ord < 128) {
        return chr($ord);
    } elseif ($ord < 2048) {
        $no_bytes = 2;
    } elseif ($ord < 65536) {
        $no_bytes = 3;
    } elseif ($ord < 1114112) {
        $no_bytes = 4;
    } else {
        return '';
    }

    switch ($no_bytes) {
        case 2: {
                $prefix = array(
                    31,
                    192
                );
                break;
            }
        case 3: {
                $prefix = array(
                    15,
                    224
                );
                break;
            }
        case 4: {
                $prefix = array(
                    7,
                    240
                );
            }
    }

    for ($i = 0; $i < $no_bytes; $i ++) {
        $byte[$no_bytes - $i - 1] = (
                ($ord & (63 * pow(2, 6 * $i))) / pow(2, 6 * $i)) &
                63 | 128;
    }

    $byte[0] = ($byte[0] & $prefix[0]) | $prefix[1];

    $ret = '';
    for ($i = 0; $i < $no_bytes; $i ++) {
        $ret .= chr($byte[$i]);
    }

    return $ret;
}
