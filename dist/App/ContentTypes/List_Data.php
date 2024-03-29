<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Exceptions\DatabaseException;

// A list is a paginated set of content
// filtered by conditions
// e.g. article archive
class List_Data extends Model {
    public $content_id = null;

    public $language = null;

    public $category_id = null;

    public $menu = null;

    public $parent_id = null;

    public $order_by = 'title';

    public $order_direction = 'asc';

    public $limit = null;

    public $use_pagination = false;

    public $type = null;

    public function filter(?int $offset = null): array {
        return $this->filterPaginated($offset);
    }

    public function hasMore(int $offset = 0): bool {
        return count(
            $this->filterPaginated(
                $offset + $this->limit
            )
        ) > 0;
    }

    // apply the filter conditions of this list
    // returns array of contents
    public function filterPaginated(?int $offset = null): array {
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
    public function filterAll(): array {
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

    public function loadByID($id): void {
        $id = (int)$id;
        $result = Database::query('select * from ' . Database::tableName('lists')
                        . " WHERE content_id = {$id}");
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $this->fillVars($dataset);
        }

        $this->content_id = $id ? (int)$id : null;
    }

    public function save(): void {
        if ($this->content_id === null) {
            throw new DatabaseException('no content_id for list set');
        }
        $id = (int)($this->content_id);
        $result = Database::query('select * from ' . Database::tableName('lists')
                        . " WHERE content_id = {$id}");
        if (Database::getNumRows($result) > 0) {
            $this->update();
        } else {
            $this->create();
        }
    }

    public function isPersistent(): bool {
        return $this->content_id >= 1;
    }

    protected function fillVars($data = null): void {
        $this->content_id = $data->content_id ? (int)($data->content_id) : null;
        $this->language = $data->language ?: null;
        $this->category_id = $data->category_id ? (int)($data->category_id) : null;
        $this->menu = $data->menu ?: null;
        $this->parent_id = $data->parent_id ? (int)($data->parent_id) : null;
        $this->order_by = $data->order_by ?: null;
        $this->order_direction = $data->order_direction ?: null;
        $this->limit = $data->limit ? (int)($data->limit) : null;
        $this->use_pagination = (bool)$data->use_pagination;
        $this->type = $data->type ?: null;
    }

    protected function create(): void {
        $content_id = (int)($this->content_id);

        if ($this->language === null) {
            $language = 'null';
        } else {
            $language = "'" . Database::escapeValue($this->language) . "'";
        }

        if ($this->category_id === null || $this->category_id === 0) {
            $category_id = 'null';
        } else {
            $category_id = (int)$this->category_id;
        }

        if ($this->menu === null) {
            $menu = 'null';
        } else {
            $menu = "'" . Database::escapeValue($this->menu) . "'";
        }

        if ($this->parent_id === null || $this->parent_id === 0) {
            $parent_id = 'null';
        } else {
            $parent_id = (int)($this->parent_id);
        }
        if ($this->order_by === null) {
            $order_by = 'null';
        } else {
            $order_by = "'" . Database::escapeValue($this->order_by) . "'";
        }
        if ($this->order_direction === 'desc') {
            $order_direction = 'desc';
        } else {
            $order_direction = 'asc';
        }

        $use_pagination = (int)($this->use_pagination);

        if ($this->type === null || $this->type == 'null') {
            $type = 'null';
        } else {
            $type = "'" . Database::escapeValue($this->type) . "'";
        }

        $limit = 'null';
        if ((int)($this->limit) > 0) {
            $limit = (int)($this->limit);
        }
        $sql = 'INSERT INTO ' . Database::tableName('lists') .
                ' (content_id, language, category_id, menu, parent_id, '
                . '`order_by`, `order_direction`, `limit`, `use_pagination`, '
                . "`type`) values ({$content_id}, {$language},
		{$category_id}, {$menu}, {$parent_id}, {$order_by}, "
                . "'{$order_direction}', {$limit}, {$use_pagination}, {$type})";
        Database::query($sql);
    }

    protected function update(): void {
        $content_id = (int)$this->content_id;

        if ($this->language === null) {
            $language = 'null';
        } else {
            $language = "'" . Database::escapeValue($this->language) . "'";
        }

        if ($this->category_id === null || $this->category_id === 0) {
            $category_id = 'null';
        } else {
            $category_id = (int)$this->category_id;
        }

        if ($this->menu === null) {
            $menu = 'null';
        } else {
            $menu = "'" . Database::escapeValue($this->menu) . "'";
        }

        if ($this->parent_id === null || $this->parent_id === 0) {
            $parent_id = 'null';
        } else {
            $parent_id = (int)($this->parent_id);
        }

        if ($this->order_by === null) {
            $order_by = 'null';
        } else {
            $order_by = "'" . Database::escapeValue($this->order_by) . "'";
        }

        if ($this->order_direction === 'desc') {
            $order_direction = 'desc';
        } else {
            $order_direction = 'asc';
        }

        $limit = 'null';
        if ((int)$this->limit > 0) {
            $limit = (int)$this->limit;
        }

        if ($this->type === null || $this->type == 'null') {
            $type = 'null';
        } else {
            $type = "'" . Database::escapeValue($this->type) . "'";
        }

        $use_pagination = (int)($this->use_pagination);

        $sql = 'UPDATE ' . Database::tableName('lists') . " set language = {$language},
		category_id = {$category_id}, menu = {$menu},"
                . "parent_id = {$parent_id}, `order_by` = {$order_by},"
                . "`order_direction` = '{$order_direction}', `limit` = {$limit},"
                . "`use_pagination` = {$use_pagination}, `type` = {$type}"
                . " where content_id = {$content_id} ";
        Database::query($sql);
    }
}
