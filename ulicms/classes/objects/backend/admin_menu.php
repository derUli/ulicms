<?php
class AdminMenu {
	private $children = array ();
	public function __construct($children = array()) {
		$this->children = $children;
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
	public function outputMenu() {
		$html = "<ul>";
		foreach ( $this->children as $child ) {
			if (get_action () == $child->getIdentifier ()) {
				$html .= '<li class="active">';
			} else {
				$html .= "</li>";
			}
			$html .= '<a href="' . $child->getLink () . '">';
			$html .= $child->getLink ();
			$html .= "</a>";
			$html .= "</li>";
		}
	}
}