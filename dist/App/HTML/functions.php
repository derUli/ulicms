<?php

declare(strict_types=1);

namespace App\HTML;

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Exceptions\FileNotFoundException;
use App\Utils\File;
use ModuleHelper;

/**
 * Replaces HTML entities and replaces linebreaks with <br>
 *
 *
 * @param string $str
 * @return string
 */
function text($str): string {
    return \nl2br(\_esc($str));
}

/**
 * Generates an image HTML tag
 *
 * @param string $file
 * @param array<string, string> $htmlAttributes
 *
 * @return string
 */
function imageTag(string $file, array $htmlAttributes = []): string {
    if (! isset($htmlAttributes['src'])) {
        $htmlAttributes['src'] = $file;
    }
    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<img {$attribHTML}>";
}

/**
 * Generates a link that looks like a button
 *
 * @param string $url
 * @param string $text
 * @param string $type
 * @param bool $allowHtml
 * @param ?string $target
 * @param array<string, string> $htmlAttributes
 *
 * @return string
 */
function buttonLink(
    string $url,
    string $text,
    string $type,
    bool $allowHtml = false,
    ?string $target = null,
    array $htmlAttributes = []
): string {
    if (! isset($htmlAttributes['class'])) {
        $htmlAttributes['class'] = $type;
    } else {
        $htmlAttributes['class'] = "{$type} {$htmlAttributes['class']}";
    }

    return link($url, $text, $allowHtml, $target, $htmlAttributes);
}

/**
 * Generates a link
 *
 * @param string $url
 * @param string $text
 * @param bool $allowHtml
 * @param ?string $target
 * @param array<string, string> $htmlAttributes
 *
 * @return string
 */
function link(
    string $url,
    string $text,
    bool $allowHtml = false,
    ?string $target = null,
    array $htmlAttributes = []
): string {
    $htmlAttributes['href'] = $url;
    if ($target) {
        $htmlAttributes['target'] = $target;
    }

    if (! $allowHtml) {
        $text = _esc($text);
    }

    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);

    return "<a {$attribHTML}>{$text}</a>";
}

/**
 * Generates a font awesome icon
 *
 * @param string $classes
 * @param array<string, string> $htmlAttributes
 *
 * @return string
 */
function icon(string $classes, array $htmlAttributes = []): string {
    if (! isset($htmlAttributes['class'])) {
        $htmlAttributes['class'] = $classes;
    } else {
        $htmlAttributes['class'] = "{$classes} {$htmlAttributes['class']}";
    }

    $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
    return "<i {$attribHTML}></i>";
}

/**
 * Generates an image tag with base64 uri encoding image
 *
 * @param string $file
 * @param array<string, string> $htmlAttributes
 *
 * @return string
 */
function imageTagInline(string $file, array $htmlAttributes = []): string {
    $url = File::toDataUri($file);
    if (! $url) {
        throw new FileNotFoundException("Image {$file} not found");
    }

    return imageTag($url, $htmlAttributes);
}

/**
 * Checks if a string contains HTML code
 * @param string $string
 *
 * @return bool
 */
function stringContainsHtml(string $string): bool {
    return $string != strip_tags($string);
}
