<?php

use UliCMS\Exceptions\NotImplementedException;

class RotatingText extends Model {

    private $animation;
    private $separator = ",";
    private $speed = 2000;
    private $words = null;

    public function loadByID($id) {
        throw new NotImplementedException("load not implemented");
    }

    public function getAnimation() {
        return $this->animation;
    }

    public function getSeparator() {
        return $this->separator;
    }

    public function getSpeed() {
        return $this->speed;
    }

    public function getWords() {
        return $this->words;
    }

    public function setAnimation($val) {
        $this->animation = !is_blank($val) ? strval($val) : null;
    }

    public function setSeparator($val) {
        $this->separator = !is_blank($val) ? strval($val) : null;
    }

    public function setSpeed($val) {
        $this->speed = intval($val);
    }

    public function setWords($val) {
        $this->words = !is_blank($val) ? strval($val) : null;
    }

    protected function fillVars($query = null) {
        throw new NotImplementedException("fillVars not implemented");
    }

    protected function insert() {
        Database::pQuery("insert into {prefix}rotating_text
            (animation, separator, speed, words)
            values(?, ?, ?, ?)", array(
            $this->getAnimation(), $this->getSeparator(),
            $this->getSpeed(), $this->getWords()
                ), true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update() {
        Database::pQuery("update {prefix}rotating_text
            set animation = ?, separator = ?, speed = ?, words = ?
            where id = ?", array(
            $this->getAnimation(), $this->getSeparator(),
            $this->getSpeed(), $this->getWords(),
            $this->getId()
                ), true);
        $this->setID(Database::getLastInsertID());
    }

    public function delete() {
        throw new NotImplementedException("delete not implemented");
    }

}
