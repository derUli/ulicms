<?php

class Snippet extends Page {

    public $type = "snippet";
    public $menu = "not_in_menu";
    public $hidden = true;

    protected function fillVarsByResult($result) {
        parent::fillVarsByResult($result);
        $this->type = "snippet";
        $this->mehu = "not_in_menu";
        $this->hidden = true;
    }

}
