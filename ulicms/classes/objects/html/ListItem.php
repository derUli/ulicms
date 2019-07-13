<?php

declare(strict_types=1);

namespace UliCMS\HTML;

use Template;

class ListItem {

    private $value;
    private $text;
    private $selected;

    public function __construct($value, $text, bool $selected = false) {
        $this->value = $value;
        $this->text = $text;
        $this->selected = $selected;
    }

    public function getHtml(): string {
        if ($this->selected) {
            return '<option value="' . Template::getEscape($this->value) . '" selected>' . Template::getEscape($this->text) . '</option>';
        }
        return '<option value="' . Template::getEscape($this->value) . '">' . Template::getEscape($this->text) . '</option>';
    }

    public function __toString(): string {
        return $this->getHtml();
    }

    public function render(): void {
        echo $this->getHtml();
    }

    public function getValue() {
        return $this->value;
    }

    public function getText() {
        return $this->text;
    }

    public function getSelected(): bool {
        return $this->selected;
    }

    public function setValue($val) {
        $this->value = !is_null($val) ? strval($val) : null;
    }

    public function setText($val): void {
        $this->text = !is_null($val) ? strval($val) : null;
    }

    public function setSelected(bool $val): void {
        $this->selected = $val;
    }

}
