<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Models\Content\ListData;

class Content_List extends Page {

    public $listData = null;
    public $type = "list";

    public function __construct($id = null) {
        parent::__construct($id);
        $this->listData = new ListData();
    }

    protected function fillVars($result = null): void {
        parent::fillVars($result);
        $this->listData = new ListData($this->id);
    }

    public function getIcon(): string {
        return "fas fa-list-ul";
    }

}
