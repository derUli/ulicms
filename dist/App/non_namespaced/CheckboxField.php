<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class CheckboxField extends CustomField
{
    public function render($value = null): string
    {
        \App\Storages\ViewBag::set('field', $this);
        \App\Storages\ViewBag::set('field_value', (int)$value);
        \App\Storages\ViewBag::set('field_name', $this->contentType !== null ?
                        $this->contentType . '_' . $this->name : $this->name);

        return Template::executeDefaultOrOwnTemplate(
            'fields/checkboxfield.php'
        );
    }
}
