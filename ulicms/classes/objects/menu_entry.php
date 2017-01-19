<?php
class MenuEntry {
	private $title;
	private $link;
	private $identifier;
	public function __construct($title, $link, $identifier) {
		$this->title = $title;
		$this->link = $link;
		$this->identifier = $identifier;
	}
	public function getTitle() {
		return $this->title;
	}
	public function getLink() {
		return $this->link;
	}
	public function getIdentifier() {
		return $this->identifier;
	}
	public function setTitle($value) {
		$this->title = $value;
	}
	public function setLink($value) {
		$this->link = $value;
	}
	public function setIdentifier($value) {
		$this->identifier = $value;
	}
}