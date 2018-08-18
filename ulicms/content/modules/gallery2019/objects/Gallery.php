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

    public function tearDown()
    {
        Database::query("delete from `{prefix}gallery` where title like 'Test - %'", true);
    }

    public function loadByID($id)
    {
        $sql = "select * from `{prefix}gallery` where id = ?";
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

    protected function insert()
    {
        $time = time();
        $this->setCreated($time);
        $this->setUpdated($time);
        $sql = "insert into `{prefix}gallery` 
                (
                    title, 
                    created, 
                    updated, 
                    createdby, 
                    lastchangedby
                )
                values
                (
                    ?,
                    FROM_UNIXTIME(?),
                    FROM_UNIXTIME(?),
                    ?,
                    ?
                )";
        $args = array(
            $this->getTitle(),
            $this->getCreated(),
            $this->getUpdated(),
            $this->getCreatedBy(),
            $this->getlastchangedby()
        );
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update()
    {
        if (! $this->getID()) {
            return;
        }
        $this->setUpdated(time());
        $sql = "update `{prefix}gallery` set
                title = ?,
                created = FROM_UNIXTIME(?),
                updated = FROM_UNIXTIME(?),
                createdby = ?,
                lastchangedby = ?
                where id = ?
                ";
        $args = array(
            $this->getTitle(),
            $this->getCreated(),
            $this->getUpdated(),
            $this->getCreatedBy(),
            $this->getlastchangedby(),
            $this->getID()
        );
        Database::pQuery($sql, $args, true);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($val)
    {
        $this->title = ! is_null($val) ? strval($val) : null;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($val)
    {
        $this->created = is_numeric($val) ? intval($val) : null;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($val)
    {
        $this->updated = is_numeric($val) ? intval($val) : null;
    }

    public function getCreatedBy()
    {
        return $this->createdby;
    }

    public function setCreatedBy($val)
    {
        $this->createdby = is_numeric($val) ? intval($val) : null;
    }

    public function getLastChangedBy()
    {
        return $this->lastchangedby;
    }

    public function setLastChangedBy($val)
    {
        $this->lastchangedby = is_numeric($val) ? intval($val) : null;
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