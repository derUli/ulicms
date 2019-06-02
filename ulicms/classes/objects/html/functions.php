<?php

namespace UliCMS\HTML;

use UliCMS\Exceptions\FileNotFoundException;
use ModuleHelper;
use UliCMS\Utils\File;

function text($str) {
    return \nl2br(\_esc($str));
}

function imageTag($file, $htmlAttributes = array()) {
    if (!isset($htmlAttributes["src"])) {
        $htmlAttributes["src"] = $file;
    }
    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<img {$attribHTML}>";
}

function button_link($url, $text, $type = null, $allowHtml = false, $target = null, $htmlAttributes = array()) {
    if (!isset($htmlAttributes["class"])) {
        $htmlAttributes["class"] = $type;
    } else {
        $htmlAttributes["class"] = "$type {$htmlAttributes["class"]}";
    }
    return link($url, $text, $allowHtml, $target, $htmlAttributes);
}

function link($url, $text, $allowHtml = false, $target = null, $htmlAttributes = array()) {
    $htmlAttributes["href"] = $url;
    if (is_present($target)) {
        $htmlAttributes["target"] = $target;
    }

    if (!$allowHtml) {
        $text = _esc($text);
    }

    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);

    return "<a {$attribHTML}>{$text}</a>";
}

function icon($classes, $htmlAttributes = array()) {
    $htmlAttributes["class"] = $classes;

    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<i $attribHTML></i>";
}

function imageTagInline($file, $htmlAttributes = array()) {

    $url = File::toDataUri($file);
    if (!$url) {
        new FileNotFoundException("Image {$file} not found");
    }

    return imageTag($url, $htmlAttributes);
}
