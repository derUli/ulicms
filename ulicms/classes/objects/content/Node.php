<?php

class Node extends Link
{

    public $type = "node";

    public $redirection = "#";

    protected function fillVarsByResult($result)
    {
        parent::fillVarsByResult($result);
        $this->redirection = "#";
    }
}