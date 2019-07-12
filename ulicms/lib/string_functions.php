<?php

declare(strict_types=1);

function cleanString(string $string, string $separator = '-'): string {
    return StringHelper::cleanString($string, $separator);
}

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

// TODO: Deprecate this
// Implement new methods unesc() and _unesc()
function unhtmlspecialchars(string $string): string {
    return html_entity_decode($string, ENT_COMPAT, "UTF-8");
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
    return explode($delimiters[0], strtr($string, array_combine(array_slice($delimiters, 1),
                            array_fill(0, count($delimiters) - 1, array_shift($delimiters)))));
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
function getExcerpt(string $str, int $startPos = 0, int $maxLength = 100): string {
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
        if (!faster_in_array($word, $array)) {
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
    trigger_error("global function isNullOrEmpty() is deprecated. Plese use StringHelper::isNullOrEmpty() instead."
            , E_USER_WARNING);
    return is_blank($variable);
}

function isNotNullOrEmpty($variable): bool {
    trigger_error("global function isNotNullOrEmpty() is deprecated. Plese use StringHelper::isNotNullOrEmpty() instead.", E_USER_WARNING
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

function str_replace_nth(string $search, string $replace, string $subject, int $nth): string {
    $found = preg_match_all('/' .
            preg_quote($search) . '/', $subject, $matches, PREG_OFFSET_CAPTURE);
    if (false !== $found && $found > $nth) {
        return substr_replace($subject, $replace, $matches[0][$nth][1], strlen($search));
    }
    return $subject;
}

function mb_str_split(string $string): array {
// Split at all position not after the start: ^
    // and not before the end: $
    return preg_split('/(?<!^)(?!$)/u', $string);
}

function str_replace_first(string $search, string $replace, string $subject): string {
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
