<?php

// nodes are categories for dropdown menus which
// have no content attached
class Node extends Link {

    public $type = "node";
    public $link_url = "#";

    protected function fillVars($result = null) {
        parent::fillVars($result);
        $this->link_url = "#";
    }

    public function getIcon(): string {
        return "far fa-folder";
    }

}
