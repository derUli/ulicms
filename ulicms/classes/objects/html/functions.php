<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use UliCMS\Exceptions\FileNotFoundException;
use ModuleHelper;
use UliCMS\Utils\File;

// use this to output a string as html
// html specialchars are encoded, line breaks are replaced with
// <br>
function text($str)
{
    return \nl2br(\_esc($str));
}

function _nbsp(string $text = " "): string
{
    return str_replace(" ", "&nbsp;", _esc($text));
}

function nbsp(string $text = " "): void
{
    echo _nbsp($text);
}

// generates a html img tag
function imageTag(string $file, array $htmlAttributes = []): string
{
    if (!isset($htmlAttributes["src"])) {
        $htmlAttributes["src"] = $file;
    }
    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<img {$attribHTML}>";
}

// generates a html link which looks like a button
function buttonLink(
    string $url,
    string $text,
    ?string $type = null,
    bool $allowHtml = false,
    ?string $target = null,
    array $htmlAttributes = []
): string {
    if (!isset($htmlAttributes["class"])) {
        $htmlAttributes["class"] = $type;
    } else {
        $htmlAttributes["class"] = "$type {$htmlAttributes["class"]}";
    }
    return link($url, $text, $allowHtml, $target, $htmlAttributes);
}

// generates a html link
function link(
    string $url,
    string $text,
    bool $allowHtml = false,
    ?string $target = null,
    array $htmlAttributes = []
): string {
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

// Use this method to output font-awesome icons
// e.g. icon("fas fa-cog");
function icon(string $classes, array $htmlAttributes = []): string
{
    if (!isset($htmlAttributes["class"])) {
        $htmlAttributes["class"] = $classes;
    } else {
        $htmlAttributes["class"] = "$classes {$htmlAttributes["class"]}";
    }

    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<i $attribHTML></i>";
}

// embed an image as base64 data URI
function imageTagInline(string $file, array $htmlAttributes = []): string
{
    $url = File::toDataUri($file);
    if (!$url) {
        throw new FileNotFoundException("Image {$file} not found");
    }

    return imageTag($url, $htmlAttributes);
}

// returns true if the string contains html codes
function stringContainsHtml(string $string): bool
{
    return $string != strip_tags($string);
}
