<?php
class MenuEntry {
	private $title;
	private $link;
	private $identifier;
	private $children = array ();
	public function __construct($title, $link, $identifier, $children = array()) {
		$this->title = $title;
		$this->link = $link;
		$this->identifier = $identifier;
		$this->children = $children;
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
	public function getChildren() {
		return $this->children;
	}
	public function setChildren($value) {
		$this->children = $value;
	}
	public function hasChildren() {
		return (count ( $this->children ) > 0);
	}
	public function addChild($children) {
		$this->children [] = $children;
	}
	public function getChildByID($identifier, $root = null) {
		$result = null;
		if (! $root) {
			$root = $this->children;
		}
		foreach ( $this->children as $root ) {
			if ($child->getIdentifier () == $identifier) {
				return $child;
			}
			if ($child->hasChildren ()) {
				return $this->getChildByID ( $identifier, $child );
			}
		}
		return null;
	}
}