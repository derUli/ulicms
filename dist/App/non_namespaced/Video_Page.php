<?php

declare(strict_types=1);

defined('ULICMS_ROOT') or exit('no direct script access allowed');

use App\Models\Media\Video;

// video pages are pages that are linked to video files
// video files are played with html5
class Video_Page extends Page
{
    public $type = "video";
    public $video = null;
    public $text_position = "after";

    protected function fillVars($result = null)
    {
        parent::fillVars($result);
        $this->video = $result->video ? intval($result->video) : null;
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
        $sql = "update {prefix}content set video = ?, text_position = ? "
                . "where id = ?";
        $args = array(
            $this->video,
            $this->text_position,
            $this->id
        );

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    public function getVideo(): ?Video
    {
        return $this->video ? new Video($this->video) : null;
    }

    public function setVideo(?Video $video): void
    {
        $this->video = $video ? $video->getID() : null;
    }

     /**
     * Get css classes for Font Awesome icon
     * @return string
     */
    public function getIcon(): string
    {
        return "fas fa-film";
    }
}
