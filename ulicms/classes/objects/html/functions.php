<?php

namespace UliCMS\HTML;

use UliCMS\Exceptions\FileNotFoundException;
use ModuleHelper;
use File;

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
