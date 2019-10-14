<?php

// nodes are categories for dropdown menus which
// have no content attached
class Node extends Link {

    public $type = "node";
    public $redirection = "#";

    protected function fillVars($result = null) {
        parent::fillVars($result);
        $this->redirection = "#";
    }

}
