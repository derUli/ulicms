<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use Template;

/**
 * Represents a list item in a singleSelect() or multiSelect()
 */
class ListItem
{
    private $value;

    private $text;

    private $selected;

    /**
     * Constructor
     * @param type $value
     * @param type $text
     * @param bool $selected
     */
    public function __construct($value, $text, bool $selected = false)
    {
        $this->value = $value;
        $this->text = $text;
        $this->selected = $selected;
    }

    /**
     * ListItem to string
     * @return string
     */
    public function __toString(): string
    {
        return $this->getHtml();
    }

    /**
     * Get option as HTML
     * @return string
     */
    public function getHtml(): string
    {
        if ($this->selected) {
            return '<option value="' . Template::getEscape($this->value) .
                    '" selected>' . Template::getEscape($this->text) . '</option>';
        }
        return '<option value="' . Template::getEscape($this->value) . '">' .
                Template::getEscape($this->text) . '</option>';
    }

    /**
     * Output element as HTML
     * @return void
     */
    public function render(): void
    {
        echo $this->getHtml();
    }

    /**
     * Get value
     * @return type
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get text
     * @return type
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get selected item
     * @return bool
     */
    public function getSelected(): bool
    {
        return $this->selected;
    }

    /**
     * Set value
     * @param type $val
     */
    public function setValue($val)
    {
        $this->value = $val !== null ? (string) $val : null;
    }

    /**
     * Set text
     * @param type $val
     * @return void
     */
    public function setText($val): void
    {
        $this->text = $val !== null ? (string) $val : null;
    }

    /**
     * Set selected
     * @param bool $val
     * @return void
     */
    public function setSelected(bool $val): void
    {
        $this->selected = $val;
    }
}
