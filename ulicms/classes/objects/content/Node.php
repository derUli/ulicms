<?php

class Node extends Link {

    public $type = "node";
    public $redirection = "#";

    protected function fillVars($result = null) {
        parent::fillVars($result);
        $this->redirection = "#";
    }

}
