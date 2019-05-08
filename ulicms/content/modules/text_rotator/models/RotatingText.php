<?php

use UliCMS\Backend\BackendPageRenderer;

class RotatingText extends Model {

    private $animation = "bounceIn";
    private $separator = ",";
    private $speed = 2000;
    private $words = null;

    public function loadByID($id) {
        $query = Database::pQuery("select * from {prefix}rotating_text where id = ?", array(intval($id)), true);
        $this->fillVars($query);
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

    public function getShortcode() {
        return "[rotating_text={$this->getID()}]";
    }

    public function getHtml() {
        BackendPageRenderer::setModel($this);
        return Template::executeModuleTemplate(TextRotatorController::MODULE_NAME, "rotator.php");
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
        if ($query && Database::getNumRows($query)) {
            $data = Database::fetchObject($query);
            $this->setID($data->id);
            $this->setAnimation($data->animation);
            $this->setSeparator($data->separator);
            $this->setSpeed($data->speed);
            $this->setWords($data->words);
        } else {
            $this->setID(null);
            $this->setAnimation(null);
            $this->setSeparator(null);
            $this->setSpeed(null);
            $this->setWords(null);
        }
    }

    protected function insert() {
        Database::pQuery("insert into {prefix}rotating_text
            (animation, `separator`, speed, words)
            values
            (?, ?, ?, ?)", array(
                    $this->getAnimation(), $this->getSeparator(),
                    $this->getSpeed(), $this->getWords()
                        ), true)or die(Database::getLastError());
        $this->setID(Database::getLastInsertID());
    }

    protected function update() {
        Database::pQuery("update {prefix}rotating_text
    set animation = ?, `separator` = ?, speed = ?, words = ?
    where id = ?", array(
            $this->getAnimation(), $this->getSeparator(),
            $this->getSpeed(), $this->getWords(),
            $this->getId()
                ), true);
    }

    public function delete() {
        if (!$this->getId()) {
            return;
        }
        Database::pQuery("DELETE FROM {prefix}rotating_text where id = ?", array($this->getID()), true);
        $this->setID(null);
    }

    public static function getAll($order = "id") {
        $query = Database::query("select id from {prefix}rotating_text order by {$order}", true);
        $texts = array();
        while ($row = Database::fetchObject($query)) {
            $texts[] = new self($row->id);
        }
        return $texts;
    }

}
