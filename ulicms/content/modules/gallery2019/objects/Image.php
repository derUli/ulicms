<?php
namespace Gallery2019;

use Model;
use Database;

class Image extends Model
{

    private $gallery_id;

    private $path;

    private $description;

    private $order;

    public function getGalleryId()
    {
        return $this->gallery_id;
    }

    public function getGallery()
    {
        if ($this->gallery_id == null) {
            return null;
        }
        return new Gallery($this->gallery_id);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setGalleryId($val)
    {
        $this->gallery_id = is_numeric($val) ? intval($val) : null;
    }

    public function setPath($val)
    {
        $this->path = ! is_null($val) ? strval($val) : null;
    }

    public function setDescription($val)
    {
        $this->description = ! is_null($val) ? strval($val) : null;
    }

    public function setOrder($val)
    {
        $this->order = is_numeric($val) ? intval($val) : null;
    }

    public function delete()
    {
        if (! $this->getID()) {
            return;
        }
        $sql = "delete from `{prefix}gallery` where id = ?";
        $args = array(
            $this->getID()
        );
        Database::pQuery($sql, $args, true);
        $this->fillVars(null);
    }
}