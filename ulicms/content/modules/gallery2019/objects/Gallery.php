<?php
namespace Gallery2019;

use Database;

class Gallery extends \Model
{

    private $title;

    private $created;

    private $updated;

    private $createdby;

    private $lastchangedby;

    public function loadByID($id)
    {
        $sql = "select * from {prefix}galleries where id = ?";
        $args = array(
            intval($id)
        );
        $query = Database::pQuery($sql, $args, true);
        $this->fillVars($query);
    }

    protected function fillVars($query)
    {
        if ($query and Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->setID($result->id);
            $this->title = $result->title;
            $this->created = strtotime($result->created);
            $this->updated = strtotime($result->updated);
            $this->createdby = $result->createdby;
            $this->lastchangedby = $result->lastchangedby;
        } else {
            $this->setID(null);
            $this->title = null;
            $this->created = null;
            $this->updated = null;
            $this->createdby = null;
            $this->lastchangedby = null;
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getCreatedBy()
    {
        return $this->createdby;
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