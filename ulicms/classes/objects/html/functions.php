<?php

namespace UliCMS\HTML;

function text($str) {
    return \nl2br(\_esc($str));
}

function stringContainsHtml($string) {
    return $string != strip_tags($string);
}
