<?php

use UliCMS\Exceptions\NotImplementedException;

class Model {

    protected $id = null;

    public function __construct($id = null) {
        if (!is_null($id)) {
            $this->loadByID($id);
        }
    }

    public function loadByRequestId() {
        $id = Request::getVar("id");
        if (is_numeric($id)) {
            $this->loadByID(intval($id));
        }
    }

    public function loadByID($id) {
        throw new NotImplementedException("load not implemented");
    }

    public function save() {
        if (is_null($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    protected function fillVars($query = null) {
        throw new NotImplementedException("fillVars not implemented");
    }

    protected function insert() {
        throw new NotImplementedException("insert not implemented");
    }

    protected function update() {
        throw new NotImplementedException("update not implemented");
    }

    public function delete() {
        throw new NotImplementedException("delete not implemented");
    }

    public function setID($id) {
        $this->id = is_numeric($id) ? intval($id) : null;
    }

    public function getID() {
        return $this->id;
    }

    // bind values from associative array $values to class properties
    public function bindValues($values = []) {
        $values = (array) $values;
        foreach ($values as $key => $value) {
            $camelCaseVar = ModuleHelper::underscoreToCamel($key);
            $method = "set" . ucfirst($camelCaseVar);
            // if a setter method exists, call it
            if (method_exists($this, $method)) {
                $this->$method($value);
                // if there is a class property in snake_case set it
            } else if (isset($this->$value)) {
                $this->value = $value;
                // if there is a class property in camelcase set it
            } else if (isset($this->$camelCaseVar)) {
                $this->$camelCaseVar = $value;
            }
        }
    }

    // check if $value is a variable of $type
    public static function checkValueType($value, $type, $required = false) {
        // if it's required and $value is null throw exception
        if ($required and $value === null) {
            throw new InvalidArgumentException("Required field not filled");
        }
        $isXyzFunction = "is_" . $type;
        // if it's null and not required it's ok
        if ($type === null) {
            return;
        }
        if (function_exists($isXyzFunction) and ! var_is_type($value, $type, $required)) {
            throw new InvalidArgumentException("\"{$value}\" is not of type {$type}.");
        } else if (class_exists($type) and $value instanceof $type) {
            throw new InvalidArgumentException("\"{$value}\" is not of type {$type}.");
        }
    }

    public static function getAllDatasets($tableName, $modelClass, $orderBy = "id", $where = "") {
        $result = [];
        $query = Database::selectAll($tableName, array(
                    "id"
                        ), $where, [], true, $orderBy);
        while ($row = Database::fetchObject($query)) {
            $result[] = new $modelClass($row->id);
        }
        return $result;
    }

    public function isPersistent() {
        return intval($this->getID()) >= 1;
    }

    public function hasChanges() {

        $hasChanges = false;
        $className = get_class($this);
        $originalDataset = new $className($this->getID());

        $reflection = new ReflectionClass($this);

        $vars = $reflection->getProperties();
        foreach ($vars as $property) {
            $camelCaseVar = ModuleHelper::underscoreToCamel($property->getName());
            $method = "get" . ucfirst($camelCaseVar);

            if (method_exists($this, $method)
                    and $this->$method() != $originalDataset->$method()) {
                $hasChanges = true;
            }
        }
        return $hasChanges;
    }

}
