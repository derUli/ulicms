<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use StringHelper;
use ModuleHelper;
use Template;
use UliCMS\Constants\RequestMethod;

// this class contains method to build <form> tags
class Form {

    public static function buildMethodCallForm(string $sClass,
            string $sMethod,
            array $otherVars = [],
            string $requestMethod = RequestMethod::POST,
            array $htmlAttributes = []): string {
        $html = "";
        $attribhtml = StringHelper::isNotNullOrWhitespace(ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes)) ? " " . ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes) : "";
        $html .= '<form action="index.php" method="' . $requestMethod . '"' . $attribhtml . '>';
        $html .= get_csrf_token_html();
        $args = $otherVars;
        $args["sClass"] = $sClass;
        $args["sMethod"] = $sMethod;
        foreach ($args as $key => $value) {
            $html .= '<input type="hidden" name="' . Template::getEscape($key) . '" value="' . Template::getEscape($value) . '">';
        }
        return $html;
    }

    public static function buildMethodCallButton(string $sClass,
            string $sMethod,
            string $buttonText,
            array $buttonAttributes = [
                "class" => "btn btn-default",
                "type" => "submit"],
            array $otherVars = [],
            array $formAttributes = [],
            string $requestMethod = RequestMethod::POST): string {
        $html = ModuleHelper::buildMethodCallForm($sClass, $sMethod, $otherVars, $requestMethod, $formAttributes);
        $html .= '<button ' . ModuleHelper::buildHTMLAttributesFromArray($buttonAttributes) . ">";
        $html .= $buttonText . "</button>";
        $html .= "</form>";
        return $html;
    }

    public static function deleteButton(string $url,
            array $otherVars = [],
            array $htmlAttributes = []) {
        $html = "";
        $htmlAttributes["class"] = trim("delete-form " . $htmlAttributes["class"]);

        $attribhtml = StringHelper::isNotNullOrWhitespace(ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes)) ? " " . ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes) : "";

        $html .= '<form action="' . _esc($url) . '" method="' . RequestMethod::POST . '"' . $attribhtml . '>';
        $html .= get_csrf_token_html();
        foreach ($otherVars as $key => $value) {
            $html .= '<input type="hidden" name="' . Template::getEscape($key) . '" value="' . Template::getEscape($value) . '">';
        }
        $imgFile = is_admin_dir() ? "gfx/delete.gif" : "admin/gfx/delete.gif";
        $html .= '<input type="image" src="' . $imgFile . '" alt="' . get_translation("delete") . '" title="' . get_translation("delete") . '">';
        $html .= "</form>";
        return optimizeHtml($html);
    }

    public static function endForm(): string {
        return "</form>";
    }

}
