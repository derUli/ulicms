<?php

class Content_List extends Page {

    public $listData = null;
    public $type = "list";

    public function __construct() {
        parent::__construct();
        $this->listData = new List_Data();
    }

    protected function fillVars($result = null) {
        parent::fillVars($result);
        $this->listData = new List_Data($this->id);
    }

}
