<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use UliCMS\Exceptions\FileNotFoundException;
use ModuleHelper;
use UliCMS\Utils\File;

function text($str) {
    return \nl2br(\_esc($str));
}

function imageTag(string $file, array $htmlAttributes = []): string {
    if (!isset($htmlAttributes["src"])) {
        $htmlAttributes["src"] = $file;
    }
    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<img {$attribHTML}>";
}

function buttonLink(string $url,
        string $text,
        ?string $type = null,
        bool $allowHtml = false,
        ?string $target = null,
        array $htmlAttributes = []): string {
    if (!isset($htmlAttributes["class"])) {
        $htmlAttributes["class"] = $type;
    } else {
        $htmlAttributes["class"] = "$type {$htmlAttributes["class"]}";
    }
    return link($url, $text, $allowHtml, $target, $htmlAttributes);
}

function link(string $url,
        string $text,
        bool $allowHtml = false,
        ?string $target = null,
        array $htmlAttributes = []): string {
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

function icon(string $classes, array $htmlAttributes = []): string {
    $htmlAttributes["class"] = $classes;

    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<i $attribHTML></i>";
}

function imageTagInline(string $file, array $htmlAttributes = []): string {

    $url = File::toDataUri($file);
    if (!$url) {
        new FileNotFoundException("Image {$file} not found");
    }

    return imageTag($url, $htmlAttributes);
}

function stringContainsHtml(string $string): bool {
    return $string != strip_tags($string);
}
