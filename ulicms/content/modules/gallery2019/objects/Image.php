<?php
namespace Gallery2019;

use Model;
use Database;
use Path;
use StringHelper;
use NotImplementedException;

class Image extends Model
{

    private $gallery_id;

    private $path;

    private $description;

    private $order = 0;

    public function loadByID($id)
    {
        $sql = "select * from `{prefix}gallery_images` where id = ?";
        $args = array(
            intval($id)
        );
        $query = Database::pQuery($sql, $args, true);
        $this->fillVars($query);
    }

    protected function fillVars($query = null)
    {
        if ($query and Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->setID($result->id);
            $this->gallery_id = $result->gallery_id;
            $this->path = $result->path;
            $this->description = $result->description;
            $this->order = $result->order;
        } else {
            $this->setID(null);
            $this->gallery_id = null;
            $this->path = null;
            $this->description = null;
            $this->order = 0;
        }
    }

    protected function insert()
    {
        $sql = "insert into `{prefix}gallery_images`
                (
                    gallery_id,
                    path,
                    description,
                    `order`
                ) 
                VALUES 
                (
                    ?,
                    ?,
                    ?,
                    ?
                )";
        $args = array(
            $this->gallery_id,
            $this->path,
            $this->description,
            $this->order
        );
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update()
    {
        $sql = "update `{prefix}gallery_images` set 
                    gallery_id = ?,
                    path = ?,
                    description = ?,
                    `order` = ?
                    where id = ?
                    ";
        $args = array(
            $this->gallery_id,
            $this->path,
            $this->description,
            $this->order,
            $this->getID()
        );
        Database::pQuery($sql, $args, true);
    }

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

    public function exists()
    {
        if (StringHelper::isNullOrWhitespace($this->path)) {
            return false;
        }
        $path = urldecode(remove_prefix($this->path, "/"));
        
        $fullPath = Path::resolve("ULICMS_DATA_STORAGE_ROOT/" . $path);
        return is_file($path);
    }

    public function delete()
    {
        if (! $this->getID()) {
            return;
        }
        $sql = "delete from `{prefix}gallery_images` where id = ?";
        $args = array(
            $this->getID()
        );
        Database::pQuery($sql, $args, true);
        $this->fillVars(null);
    }
}