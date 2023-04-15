<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

class FileImage extends CustomField
{
    public function render($value = null): string
    {
        \App\Storages\ViewBag::set('field', $this);
        \App\Storages\ViewBag::set('field_value', $value);
        \App\Storages\ViewBag::set('field_name', $this->contentType !== null ?
                        $this->contentType . '_' . $this->name : $this->name);

        \App\Storages\ViewBag::set('fm_type', 'images');

        return Template::executeDefaultOrOwnTemplate('fields/file.php');
    }
}
