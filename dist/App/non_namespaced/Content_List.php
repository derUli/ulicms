<?php

declare(strict_types=1);

class Content_List extends Page
{
    public $listData = null;
    public $type = "list";

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->listData = new List_Data();
    }

    protected function fillVars($result = null): void
    {
        parent::fillVars($result);
        $this->listData = new List_Data($this->id);
    }

     /**
     * Get css classes for Font Awesome icon
     * @return string
     */
    public function getIcon(): string
    {
        return "fas fa-list-ul";
    }
}
