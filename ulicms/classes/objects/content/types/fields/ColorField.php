<?php

class ColorField extends CustomField
{

    public $htmlAttributes = array(
        "class" => "jscolor {hash:true,caps:true}"
    );

    public function render($value = null)
    {
        if (! isset($this->htmlAttributes["class"])) {
            $this->htmlAttributes["class"] = "jscolor {hash:true,caps:true}";
        }
        ViewBag::set("field", $this);
        ViewBag::set("field_value", $value);
        ViewBag::set("field_name", ! is_null($this->contentType) ? $this->contentType . "_" . $this->name : $this->name);
        
        return Template::executeDefaultOrOwnTemplate("fields/textfield.php");
    }
}

// Alias
class ColourField extends ColorField
{
}