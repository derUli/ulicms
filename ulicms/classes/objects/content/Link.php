<?php

declare(strict_types=1);

class Link extends Page {

    public $redirection = "";
    public $type = "link";

    public function save() {
        $retval = null;
        if ($this->id === null) {
            $retval = $this->create();
            $this->update();
        } else {
            $retval = $this->update();
        }
        return $retval;
    }

    protected function fillVars($result = null) {
        parent::fillVars($result);
        $this->redirection = $result->redirection;
    }

    public function update() {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = "update {prefix}content set redirection = ? where id = ?";
        $args = array(
            $this->redirection,
            $this->id
        );

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    public function isRegular(): bool {
        return false;
    }

}
