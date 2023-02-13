<?php

declare(strict_types=1);

class HtmlField extends CustomField
{
    public function render($value = null): string
    {
        if (!isset($this->htmlAttributes["class"])) {
            $this->htmlAttributes["class"] = get_html_editor();
        }
        if (get_html_editor() == "codemirror") {
            $this->htmlAttributes["data-mimetype"] = "text/html";
        }
        ViewBag::set("field", $this);
        ViewBag::set("field_value", $value);
        ViewBag::set("field_name", $this->contentType !== null ?
                        $this->contentType . "_" . $this->name : $this->name);

        return Template::executeDefaultOrOwnTemplate("fields/htmlfield.php");
    }
}
