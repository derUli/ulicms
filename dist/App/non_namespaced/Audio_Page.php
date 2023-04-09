<?php

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Models\Media\Audio;

// audio pages are pages that are linked to audio files
// audio files are played with html5

class Audio_Page extends Page
{
    public $audio = null;
    public $type = 'audio';
    public $text_position = 'after';

    protected function fillVars($result = null)
    {
        parent::fillVars($result);
        $this->audio = $result->audio;
        $this->text_position = $result->text_position;
    }

    public function save()
    {
        $retval = null;
        if ($this->id === null) {
            $retval = $this->create();
            $this->update();
        } else {
            $retval = $this->update();
        }
        return $retval;
    }

    public function update()
    {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = 'update {prefix}content set audio = ?, '
                . 'text_position = ? where id = ?';
        $args = [
            $this->audio,
            $this->text_position,
            $this->id
        ];

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    public function getAudio(): ?Audio
    {
        return $this->audio ? new Audio($this->audio) : null;
    }

    public function setAudio(?Audio $audio): void
    {
        $this->audio = $audio ? $audio->getID() : null;
    }


     /**
     * Get css classes for Font Awesome icon
     * @return string
     */
    public function getIcon(): string
    {
        return 'fas fa-volume-up';
    }
}
