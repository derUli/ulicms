<?php

declare(strict_types=1);

class SelectField extends CustomField
{
    public $options = [];
    public $translateOptions = true;

    public function render($value = null): string
    {
        ViewBag::set("field", $this);
        ViewBag::set("field_value", $value);
        ViewBag::set("field_options", $this->options);
        ViewBag::set("field_name", $this->contentType !== NULL?
                        $this->contentType . "_" . $this->name : $this->name);

        return Template::executeDefaultOrOwnTemplate("fields/selectfield.php");
    }
}
