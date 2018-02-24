<?php

namespace UliCMS\HTML;

use Template;

class ListItem {
	private $value;
	private $text;
	private $selected;
	public function __construct($value, $text, $selected = false) {
		$this->value = $value;
		$this->text = $text;
		$this->selected = $selected;
	}
	public function getHtml() {
		if ($this->selected) {
			return '<option value="' . Template::getEscape ( $this->value ) . '" selected>' . Template::getEscape ( $this->text ) . '</option>';
		} else {
			return '<option value="' . Template::getEscape ( $this->value ) . '">' . Template::getEscape ( $this->text ) . '</option>';
		}
	}
	public function __toString() {
		return $this->getHtml ();
	}
	public function render() {
		echo $this->getHtml ();
	}
	public function getValue() {
		return $this->value;
	}
	public function getText() {
		return $this->text;
	}
	public function getSelected() {
		return $this->selected;
	}
	public function setValue($val) {
		$this->value = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setText($val) {
		$this->text = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setSelected($val) {
		$this->selected = boolval ( $val );
	}
}