<?php

class FileFile extends CustomField
{

    public function render($value = null)
    {
        ViewBag::set("field", $this);
        ViewBag::set("field_value", $value);
        ViewBag::set("field_name", ! is_null($this->contentType) ? $this->contentType . "_" . $this->name : $this->name);
        
        ViewBag::set("kcfinder_type", "files");
        
        return Template::executeDefaultOrOwnTemplate("fields/file.php");
    }
}