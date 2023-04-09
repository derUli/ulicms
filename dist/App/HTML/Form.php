<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Constants\RequestMethod;
use ModuleHelper;
use Template;

/**
 * This class contains method to build <form> tags
 */
class Form
{
    /**
     * Generates method call form
     * @param string $sClass
     * @param string $sMethod
     * @param array $otherVars
     * @param string $requestMethod
     * @param array $htmlAttributes
     * @return string
     */
    public static function buildMethodCallForm(
        string $sClass,
        string $sMethod,
        array $otherVars = [],
        string $requestMethod = RequestMethod::POST,
        array $htmlAttributes = []
    ): string {
        $html = '';
        $attribhtml = ! empty(
            ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes)
        ) ?
                ' ' .
                ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes) :
                '';
        $html .= '<form action="index.php" method="' . $requestMethod . '"'
                . $attribhtml . '>';
        $html .= get_csrf_token_html();
        $args = $otherVars;
        $args['sClass'] = $sClass;
        $args['sMethod'] = $sMethod;
        foreach ($args as $key => $value) {
            $html .= '<input type="hidden" name="' .
                    Template::getEscape($key) . '" value="' .
                    Template::getEscape($value) . '">';
        }


        return $html;
    }

    /**
     * Generates method call button
     * @param string $sClass
     * @param string $sMethod
     * @param string $buttonText
     * @param array $buttonAttributes
     * @param array $otherVars
     * @param array $formAttributes
     * @param string $requestMethod
     * @return string
     */
    public static function buildMethodCallButton(
        string $sClass,
        string $sMethod,
        string $buttonText,
        array $buttonAttributes = [
            'class' => 'btn btn-default',
            'type' => 'submit'],
        array $otherVars = [],
        array $formAttributes = [],
        string $requestMethod = RequestMethod::POST
    ): string {
        $html = ModuleHelper::buildMethodCallForm(
            $sClass,
            $sMethod,
            $otherVars,
            $requestMethod,
            $formAttributes
        );
        $html .= '<button ' .
                ModuleHelper::buildHTMLAttributesFromArray($buttonAttributes)
                . '>';
        $html .= $buttonText . '</button>';
        $html .= '</form>';


        return $html;
    }

    /**
     * Generates delete button
     * @param string $url
     * @param array $otherVars
     * @param array $htmlAttributes
     * @return type
     */
    public static function deleteButton(
        string $url,
        array $otherVars = [],
        array $htmlAttributes = []
    ) {
        $html = '';

        if (! isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = '';
        }

        $htmlAttributes['class'] = trim('delete-form ' . $htmlAttributes['class']);

        $htmlAttributesString = ModuleHelper::buildHTMLAttributesFromArray($htmlAttributes);
        $attribHtml = '';

        if (! empty($htmlAttributes)) {
            $attribHtml .= " $htmlAttributesString";
        }

        $html .= '<form action="' . _esc($url) . '" method="' .
                RequestMethod::POST . '"' . $attribHtml . '>';
        $html .= get_csrf_token_html();
        foreach ($otherVars as $key => $value) {
            $html .= '<input type="hidden" name="' . Template::getEscape($key)
                    . '" value="' . Template::getEscape($value) . '">';
        }
        $imgFile = is_admin_dir() ? 'gfx/delete.png' : 'admin/gfx/delete.png';
        $html .= '<input type="image" src="' . $imgFile . '" alt="' .
                get_translation('delete') . '" title="' .
                get_translation('delete') . '">';
        $html .= '</form>';
        return optimizeHtml($html);
    }

    /**
     * Returns closing tag for HTML form
     * @return string
     */
    public static function endForm(): string
    {
        return '</form>';
    }
}
