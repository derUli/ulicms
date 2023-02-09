<?php

declare(strict_types=1);

class FileFile extends CustomField
{
    public function render($value = null): string
    {
        ViewBag::set("field", $this);
        ViewBag::set("field_value", $value);
        ViewBag::set("field_name", $this->contentType !== NULL?
                        $this->contentType . "_" . $this->name : $this->name);

        ViewBag::set("fm_type", "files");

        return Template::executeDefaultOrOwnTemplate("fields/file.php");
    }
}
