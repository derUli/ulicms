<?php

declare(strict_types=1);

namespace App\HTML;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Template;

/**
 * Represents a list item in a singleSelect() or multiSelect()
 */
class ListItem
{
    private mixed $value;

    private string $text;

    private bool $selected;

    /**
     * Constructor
     * 
     * @param mixed $value
     * @param string $text
     * @param bool $selected
     * 
     */
    public function __construct($value, string $text, bool $selected = false)
    {
        $this->value = $value;
        $this->text = $text;
        $this->selected = $selected;
    }

    /**
     * Get value
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get text
     * 
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get selected item
     * 
     * @return bool
     */
    public function getSelected(): bool
    {
        return $this->selected;
    }

    /**
     * Set value
     * 
     * @param mixed $val
     * @return void
     */
    public function setValue(mixed $val): void
    {
        $this->value = $val;
    }

    /**
     * Set text
     * 
     * @param string $val
     * 
     * @return void
     */
    public function setText(string $val): void
    {
        $this->text = $val;
    }

    /**
     * Set selected
     * 
     * @param bool $val
     * 
     * @return void
     */
    public function setSelected(bool $val): void
    {
        $this->selected = $val;
    }

    
    /**
     * ListItem to string
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->getHtml();
    }

    /**
     * Get option as HTML
     * 
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
     * 
     * @return void
     */
    public function render(): void
    {
        echo $this->getHtml();
    }
}
