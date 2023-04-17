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
    public string $name;

    public ?string $title = null;

    public bool $required = false;

    public ?string $helpText = null;

    public mixed $defaultValue;

    /**
     * @var array<string, string>
     */
    public array $htmlAttributes = [];

    public ?string $contentType = null;

    /**
     * Render custom field as html
     * @param mixed $value
     * @throws NotImplementedException
     * @return string
     */
    public function render(mixed $value = null): string
    {
        throw new NotImplementedException();
    }
}
