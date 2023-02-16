<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use ModuleHelper;

// this class contains helper methods to build clickable links
class Link
{
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

    public static function actionLink(
        string $action,
        string $text,
        ?string $suffix = null,
        array $htmlAttributes = []
    ): string {
        $attributes = array(
            'href' => ModuleHelper::buildActionURL($action, $suffix, true)
        );

        foreach ($htmlAttributes as $key => $value) {
            $attributes [$key] = $value;
        }
        $attribHTML = ModuleHelper::buildHTMLAttributesFromArray($attributes);

        return "<a $attribHTML>" . $text . "</a>";
    }
}
