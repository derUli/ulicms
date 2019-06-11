<?php

class AdminMenu {

    private $children = [];

    public function __construct($children = []) {
        $this->children = $children;
    }

    public function getChildren() {
        return $this->children;
    }

    public function setChildren($value) {
        $this->children = $value;
    }

    public function hasChildren() {
        return (count($this->children) > 0);
    }

    public function addChild($children) {
        $this->children[] = $children;
    }

    public function render() {
        $html = "<ul>";
        foreach ($this->children as $child) {
            if ($child->userHasPermission()) {
                $html .= "<li>";
                $targetString = '';
                if ($child->getNewWindow()) {
                    $targetString = ' target="_blank" ';
                }
                $cssClassString = "class=\"backend-menu-item-{$child->getIdentifier()}\"";
                if ($child->getIdentifier() == get_action()) {
                    $html .= '<a href="' . $child->getLink() . '" class="active"' . $targetString . $cssClassString . '>';
                } else {
                    $html .= '<a href="' . $child->getLink() . '"' . $targetString . $cssClassString . '>';
                }
                $html .= $child->getTitle();
                $html .= "</a>";
                $html .= "</li>";
            }
        }
        echo $html;
    }

}
