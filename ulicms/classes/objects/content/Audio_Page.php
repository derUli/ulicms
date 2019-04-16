<?php

class Audio_Page extends Page {

    public $audio = null;
    public $type = "audio";
    public $text_position = "after";

    protected function fillVarsByResult($result) {
        parent::fillVarsByResult($result);
        $this->audio = $result->audio;
        $this->text_position = $result->text_position;
    }

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

    public function update() {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = "update {prefix}content set audio = ?, text_position = ? where id = ?";
        $args = array(
            $this->audio,
            $this->text_position,
            $this->id
        );

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

}
