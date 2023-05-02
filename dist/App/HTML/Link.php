<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use ModuleHelper;

/**
 * Utils to build HTML links
 */
class Link {
    /**
     * Generate a <a> tag
     *
     * @param string $href
     * @param string $text
     * @param array<string, string> $htmlAttributes
     *
     * @return string
     */
    public static function link(
        string $href,
        string $text,
        array $htmlAttributes = []
    ): string {
        $attributes = [
            'href' => $href
        ];

        foreach ($htmlAttributes as $key => $value) {
            $attributes [$key] = $value;
        }
        $attribHTML = \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($attributes);

        return "<a {$attribHTML}>" . $text . '</a>';
    }

    /**
     * Returns a link to an action
     *
     * @param string $action
     * @param string $text
     * @param string|null $suffix
     *
     * @param array<string, string> $htmlAttributes
     *
     * @return string
     */
    public static function actionLink(
        string $action,
        string $text,
        ?string $suffix = null,
        array $htmlAttributes = []
    ): string {
        $url = \App\Helpers\ModuleHelper::buildActionURL($action, $suffix, true);

        return self::link($url, $text, $htmlAttributes);
    }
}
