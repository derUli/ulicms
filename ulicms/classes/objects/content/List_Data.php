<?php

use UliCMS\Exceptions\DatabaseException;

// A list is a paginated set of content
// filtered by conditions
// e.g. article archive
class List_Data extends Model
{
    public $content_id = null;
    public $language = null;
    public $category_id = null;
    public $menu = null;
    public $parent_id = null;
    public $order_by = "title";
    public $order_direction = "asc";
    public $limit = null;
    public $use_pagination = false;
    public $type = null;

    public function filter(?int $offset = null): array
    {
        return $this->filterPaginated($offset);
    }

    public function hasMore(int $offset = 0): bool
    {
        return count(
            $this->filterPaginated(
                $offset + intval($this->limit)
            )
        ) > 0;
    }

    // apply the filter conditions of this list
    // returns array of contents
    public function filterPaginated(?int $offset = null): array
    {
        $limit = $this->use_pagination ? $this->limit : null;

        return ContentFactory::getForFilter(
            $this->language,
            $this->category_id,
            $this->menu,
            $this->parent_id,
            $this->order_by,
            $this->order_direction,
            $this->type,
            $limit,
            $offset
        );
    }

    // apply the filter conditions of this list
    // returns array of contents
    public function filterAll(): array
    {
        return ContentFactory::getForFilter(
            $this->language,
            $this->category_id,
            $this->menu,
            $this->parent_id,
            $this->order_by,
            $this->order_direction,
            $this->type
        );
    }

    public function loadByID($id)
    {
        $id = intval($id);
        $result = Database::query("select * from " . tbname("lists")
                        . " WHERE content_id = $id");
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $this->fillVars($dataset);
        }

        $this->content_id = $id ? intval($id) : null;
    }

    protected function fillVars($data = null)
    {
        $this->content_id = $data->content_id ? intval($data->content_id) : null;
        $this->language = $data->language ? $data->language : null;
        $this->category_id = $data->category_id ? intval($data->category_id) : null;
        $this->menu = $data->menu ? $data->menu : null;
        $this->parent_id = $data->parent_id ? intval($data->parent_id) : null;
        $this->order_by = $data->order_by ? $data->order_by : null;
        $this->order_direction = $data->order_direction ? $data->order_direction : null;
        $this->limit = $data->limit ? intval($data->limit) : null;
        $this->use_pagination = boolval($data->use_pagination);
        $this->type = $data->type ? $data->type : null;
    }

    public function save()
    {
        if ($this->content_id === null) {
            throw new DatabaseException("no content_id for list set");
        }
        $id = intval($this->content_id);
        $result = Database::query("select * from " . tbname("lists")
                        . " WHERE content_id = $id");
        if (Database::getNumRows($result) > 0) {
            $this->update();
        } else {
            $this->create();
        }
    }

    protected function create()
    {
        $content_id = intval($this->content_id);

        if ($this->language === null) {
            $language = "null";
        } else {
            $language = "'" . Database::escapeValue($this->language) . "'";
        }

        if ($this->category_id === null or $this->category_id === 0) {
            $category_id = "null";
        } else {
            $category_id = intval($this->category_id);
        }

        if ($this->menu === null) {
            $menu = "null";
        } else {
            $menu = "'" . Database::escapeValue($this->menu) . "'";
        }

        if ($this->parent_id === null or $this->parent_id === 0) {
            $parent_id = "null";
        } else {
            $parent_id = intval($this->parent_id);
        }
        if ($this->order_by === null) {
            $order_by = "null";
        } else {
            $order_by = "'" . Database::escapeValue($this->order_by) . "'";
        }
        if ($this->order_direction === "desc") {
            $order_direction = "desc";
        } else {
            $order_direction = "asc";
        }

        $use_pagination = intval($this->use_pagination);

        if ($this->type === null || $this->type == "null") {
            $type = "null";
        } else {
            $type = "'" . Database::escapeValue($this->type) . "'";
        }

        $limit = "null";
        if (intval($this->limit) > 0) {
            $limit = intval($this->limit);
        }
        $sql = "INSERT INTO " . tbname("lists") .
                " (content_id, language, category_id, menu, parent_id, "
                . "`order_by`, `order_direction`, `limit`, `use_pagination`, "
                . "`type`) values ($content_id, $language,
		$category_id, $menu, $parent_id, $order_by, "
                . "'$order_direction', $limit, $use_pagination, $type)";
        Database::query($sql);
    }

    protected function update()
    {
        $content_id = intval($this->content_id);

        if ($this->language === null) {
            $language = "null";
        } else {
            $language = "'" . Database::escapeValue($this->language) . "'";
        }

        if ($this->category_id === null or $this->category_id === 0) {
            $category_id = "null";
        } else {
            $category_id = intval($this->category_id);
        }

        if ($this->menu === null) {
            $menu = "null";
        } else {
            $menu = "'" . Database::escapeValue($this->menu) . "'";
        }

        if ($this->parent_id === null or $this->parent_id === 0) {
            $parent_id = "null";
        } else {
            $parent_id = intval($this->parent_id);
        }

        if ($this->order_by === null) {
            $order_by = "null";
        } else {
            $order_by = "'" . Database::escapeValue($this->order_by) . "'";
        }

        if ($this->order_direction === "desc") {
            $order_direction = "desc";
        } else {
            $order_direction = "asc";
        }

        $limit = "null";
        if (intval($this->limit) > 0) {
            $limit = intval($this->limit);
        }

        if ($this->type === null || $this->type == "null") {
            $type = "null";
        } else {
            $type = "'" . Database::escapeValue($this->type) . "'";
        }

        $use_pagination = intval($this->use_pagination);

        $sql = "UPDATE " . tbname("lists") . " set language = $language,
		category_id = $category_id, menu = $menu,"
                . "parent_id = $parent_id, `order_by` = $order_by,"
                . "`order_direction` = '$order_direction', `limit` = $limit,"
                . "`use_pagination` = $use_pagination, `type` = $type"
                . " where content_id = $content_id ";
        Database::query($sql);
    }

    public function isPersistent(): bool
    {
        return $this->content_id >= 1;
    }
}
