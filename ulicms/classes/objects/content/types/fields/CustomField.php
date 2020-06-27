<?php

declare(strict_types=1);

use UliCMS\Exceptions\NotImplementedException;

class CustomField
{
    public $name;
    public $title;
    public $required = false;
    public $helpText;
    public $defaultValue = "";
    public $htmlAttributes = [];
    public $contentType;

    public function render($value = null): string
    {
        throw new NotImplementedException();
    }
}
