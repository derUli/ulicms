<?php

declare(strict_types=1);

namespace App\Models\Content\CustomFields;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Storages\ViewBag;
use Template;

class SelectField extends CustomField {
    /**
     * @var array<string, mixed>
     */
    public array $options = [];

    /**
     * @var bool
     */
    public bool $translateOptions = true;

    public function render(mixed $value = null): string {

        ViewBag::set('field', $this);
        ViewBag::set('field_value', $value);
        ViewBag::set('field_options', $this->options);
        ViewBag::set('field_name', $this->contentType !== null ?
                        $this->contentType . '_' . $this->name : $this->name);

        return Template::executeDefaultOrOwnTemplate('fields/selectfield.php');
    }
}
