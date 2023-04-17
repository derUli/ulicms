<?php

declare(strict_types=1);

namespace App\Models\Content\CustomFields;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Exceptions\NotImplementedException;

/**
 * Inherit all custom fields from this class
 */
class CustomField
{
    public $name;

    public $title;

    public $required = false;

    public $helpText;

    public $defaultValue = '';

    public $htmlAttributes = [];

    public $contentType;

    /**
     * Render custom field as html
     * @param type $value
     * @throws NotImplementedException
     * @return string
     */
    public function render($value = null): string
    {
        throw new NotImplementedException();
    }
}
