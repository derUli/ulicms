<?php

use UliCMS\Models\Content\Language;

// Links to another language
class Language_Link extends Page {

    public $link_to_language = null;
    public $type = "language_link";

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
        $this->link_to_language = $result->link_to_language;
    }

    public function update() {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = "update {prefix}content set link_to_language = ? where id = ?";
        $args = array(
            $this->link_to_language,
            $this->id
        );

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    public function getLinkedLanguage() {
        return $this->link_to_language ? new Language($this->link_to_language) : null;
    }

    public function setLinkedLanguage(?Language $language) {
        $this->link_to_language = $language ? $language->getID() : null;
    }

    public function isRegular(): bool {
        return false;
    }

}
