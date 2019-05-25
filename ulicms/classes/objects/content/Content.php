<?php

abstract class Content extends Model {

    abstract protected function loadBySlugAndLanguage($name, $language);

    public function getShowHeadline() {
        return $this->show_headline;
    }

    public static function emptyTrash() {
        Database::query("DELETE FROM {prefix}content WHERE deleted_at IS NOT NULL", true);
    }

    public function getHeadline() {
        return StringHelper::isNullOrEmpty($this->alternate_title) ? $this->title : $this->alternate_title;
    }

    public function isRegular() {
        return true;
    }

    public function getChildren($order = "id") {
        if (!$this->getID()) {
            return array();
        }
        return ContentFactory:: getAllByParent($this->getID(), $order);
    }

    public function hasChildren() {
        return count($this->getChildren());
    }

}
