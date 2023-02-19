<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use ModuleHelper;

class Link
{
    /**
     * Generate a <a> tag
     * @param string $href
     * @param string $text
     * @param array $htmlAttributes
     * @return string
     */
    public static function link(
        string $href,
        string $text,
        array $htmlAttributes = []
    ): string {
        $attributes = array(
            'href' => $href
        );

        foreach ($htmlAttributes as $key => $value) {
            $attributes [$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        return "<a $attribHTML>" . $text . "</a>";
    }

    /**
     * Returns a link to an action
     * @param string $action
     * @param string $text
     * @param string|null $suffix
     * @param array $htmlAttributes
     * @return string
     */
    public static function actionLink(
        string $action,
        string $text,
        ?string $suffix = null,
        array $htmlAttributes = []
    ): string {
        $url = ModuleHelper::buildActionURL($action, $suffix, true);

        return self::link($url, $text, $htmlAttributes);
    }
}