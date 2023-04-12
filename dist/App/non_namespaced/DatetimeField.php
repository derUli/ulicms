<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

class DatetimeField extends CustomField
{
    public function render($value = null): string
    {
        ViewBag::set('field', $this);
        ViewBag::set('field_value', $value);
        ViewBag::set('field_name', $this->contentType !== null ?
                        $this->contentType . '_' . $this->name : $this->name);

        return Template::executeDefaultOrOwnTemplate(
            'fields/datetimefield.php'
        );
    }
}
