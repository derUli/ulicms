<?php

namespace PHP81_BC;

/**
 * Replacement for strftime which is Deprecated in PHP 8.1
 * @param type $format Format String
 * @param type $time Timestamp
 * @return string Formatted Date
 */
function strftime(string $format, ?int $time): ?string {
    // Convert strftime() Format to date() Format
    $dateFormat = strftime_format_to_date_format($format);

    return date($dateFormat, $time);
}

/**
 * Convert strftime format to php date format
 * @param $strftimeformat
 * @return string|string[]
 * @throws Exception
 */
function strftime_format_to_date_format($strftimeformat) {
    $unsupported = ['%U', '%V', '%C', '%g', '%G'];
    $foundunsupported = [];
    foreach ($unsupported as $unsup) {
        if (strpos($strftimeformat, $unsup) !== false) {
            $foundunsupported[] = $unsup;
        }
    }
    if (!empty($foundunsupported)) {
        throw new \Exception("Found these unsupported chars: " . implode(",", $foundunsupported) . ' in ' . $strftimeformat);
    }
    // It is important to note that some do not translate accurately ie. lowercase L is supposed to convert to number with a preceding space if it is under 10, there is no accurate conversion so we just use 'g'
    $phpdateformat = str_replace(
            ['%a', '%A', '%d', '%e', '%u', '%w', '%W', '%b', '%h', '%B', '%m', '%y', '%Y', '%D', '%F', '%x', '%n', '%t', '%H', '%k', '%I', '%l', '%M', '%p', '%P', '%r' /* %I:%M:%S %p */, '%R' /* %H:%M */, '%S', '%T' /* %H:%M:%S */, '%X', '%z', '%Z',
                '%c', '%s',
                '%%'],
            ['D', 'l', 'd', 'j', 'N', 'w', 'W', 'M', 'M', 'F', 'm', 'y', 'Y', 'm/d/y', 'Y-m-d', 'm/d/y', "\n", "\t", 'H', 'G', 'h', 'g', 'i', 'A', 'a', 'h:i:s A', 'H:i', 's', 'H:i:s', 'H:i:s', 'O', 'T',
                'D M j H:i:s Y' /* Tue Feb 5 00:45:10 2009 */, 'U',
                '%'],
            $strftimeformat
    );
    return $phpdateformat;
}
