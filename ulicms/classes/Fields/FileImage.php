<?php

declare(strict_types=1);

namespace UliCMS\Fields;

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use ViewBag;
use Template;

class FileImage extends CustomField {

    public function render($value = null): string {
        ViewBag::set("field", $this);
        ViewBag::set("field_value", $value);
        ViewBag::set("field_name", !is_null($this->contentType) ?
                        $this->contentType . "_" . $this->name : $this->name);

        ViewBag::set("fm_type", "images");

        return Template::executeDefaultOrOwnTemplate("fields/file.php");
    }

}
