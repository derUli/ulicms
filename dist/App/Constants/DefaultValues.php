<?php

declare(strict_types=1);

namespace App\Constants;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

/**
 * Default Values
 */
abstract class DefaultValues {
    public const ALLOWED_TAGS = '<a>' .
            '<abbr>' .
            '<address>' .
            '<area>' .
            '<article>' .
            '<aside>' .
            '<audio>' .
            '<b>' .
            '<bdi>' .
            '<bdo>' .
            '<blockquote>' .
            '<br/>' .
            '<br>' .
            '<button>' .
            '<canvas>' .
            '<caption>' .
            '<cite>' .
            '<code>' .
            '<col>' .
            '<colgroup>' .
            '<command>' .
            '<data>' .
            '<datalist>' .
            '<dd>' .
            '<del>' .
            '<details>' .
            '<dfn>' .
            '<div>' .
            '<dl>' .
            '<dt>' .
            '<em>' .
            '<embed>' .
            '<fieldset>' .
            '<figcaption>' .
            '<figure>' .
            '<font>' .
            '<footer>' .
            '<form>' .
            '<h1>' .
            '<h2>' .
            '<h3>' .
            '<h4>' .
            '<h5>' .
            '<h6>' .
            '<header>' .
            '<hgroup>' .
            '<hr>' .
            '<i>' .
            '<iframe>' .
            '<img>' .
            '<input>' .
            '<ins>' .
            '<kbd>' .
            '<keygen>' .
            '<label>' .
            '<legend>' .
            '<li>' .
            '<map>' .
            '<mark>' .
            '<math>' .
            '<meter>' .
            '<nav>' .
            '<object>' .
            '<ol>' .
            '<optgroup>' .
            '<option>' .
            '<output>' .
            '<p>' .
            '<param>' .
            '<pre>' .
            '<progress>' .
            '<q>' .
            '<rp>' .
            '<rt>' .
            '<ruby>' .
            '<s>' .
            '<samp>' .
            '<section>' .
            '<select>' .
            '<small>' .
            '<source>' .
            '<span>' .
            '<strong>' .
            '<sub>' .
            '<summary>' .
            '<sup>' .
            '<svg>' .
            '<table>' .
            '<tbody>' .
            '<td>' .
            '<textarea>' .
            '<tfoot>' .
            '<th>' .
            '<thead>' .
            '<time>' .
            '<tr>' .
            '<track>' .
            '<u>' .
            '<ul>' .
            '<var>' .
            '<video>' .
            '<wbr>' .
            '<menu>';

    // Where we have to display null values
    public const NULL_VALUE = '[NULL]';
}
