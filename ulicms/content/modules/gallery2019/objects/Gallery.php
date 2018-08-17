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
        if (Database::getNumRows($query) > 0) {
            $this->bindValues(Database::fetchArray($query));
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
}