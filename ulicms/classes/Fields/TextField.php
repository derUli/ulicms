<?php

declare(strict_types=1);

namespace UliCMS\Fields;

use ViewBag;
use Template;

class TextField extends CustomField {

    public function render($value = null): string {
        ViewBag::set("field", $this);
        ViewBag::set("field_value", $value);
        ViewBag::set("field_name", !is_null($this->contentType) ?
                        $this->contentType . "_" . $this->name : $this->name);

        return Template::executeDefaultOrOwnTemplate("fields/textfield.php");
    }

}
