<?php

declare(strict_types=1);

class Link extends Page {

    public $link_url = "";
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
        $this->link_url = $result->link_url;
    }

    public function update() {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = "update {prefix}content set link_url = ? where id = ?";
        $args = array(
            $this->link_url,
            $this->id
        );

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    public function isRegular(): bool {
        return false;
    }

    public function getIcon(): string {
        return "fas fa-link";
    }

}
