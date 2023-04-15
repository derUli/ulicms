<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\HtmlEditor;

class HtmlField extends CustomField
{
    public function render($value = null): string
    {
        $htmlEditor = get_html_editor();

        if (! isset($this->htmlAttributes['class'])) {
            $this->htmlAttributes['class'] = $htmlEditor;
        }

        if ($htmlEditor == HtmlEditor::CODEMIRROR) {
            $this->htmlAttributes['data-mimetype'] = 'text/html';
        }

        \App\Storages\ViewBag::set('field', $this);
        \App\Storages\ViewBag::set('field_value', $value);
        \App\Storages\ViewBag::set('field_name', $this->contentType !== null ?
                        $this->contentType . '_' . $this->name : $this->name);

        return Template::executeDefaultOrOwnTemplate('fields/htmlfield.php');
    }
}
