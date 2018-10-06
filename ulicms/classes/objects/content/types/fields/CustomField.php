<?php
use UliCMS\Exceptions\NotImplementedException;

class CustomField
{

    public $name;

    public $title;

    public $required = false;

    public $helpText;

    public $defaultValue = "";

    public $htmlAttributes = array();

    public $contentType;

    public function render($value = null)
    {
        throw new NotImplementedException();
    }
}