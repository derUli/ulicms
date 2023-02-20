<?php

declare(strict_types=1);

class Link extends Page
{
    public $link_url = '';
    public $type = "link";

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

    protected function fillVars($result = null)
    {
        parent::fillVars($result);
        $this->link_url = $result->link_url;
    }

    public function update()
    {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = "update {prefix}content set link_url = ? where id = ?";
        $args = array(
            $this->link_url,
            $this->id
        );

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    /**
     * Check if this is content is regular
     * Regular means that it is a accessible page
     * This applies to any default contents except Link, Language_Link and Node
     * @return bool
     */
    public function isRegular(): bool
    {
        return false;
    }

     /**
     * Get css classes for Font Awesome icon
     * @return string
     */
    public function getIcon(): string
    {
        return "fas fa-link";
    }
}
