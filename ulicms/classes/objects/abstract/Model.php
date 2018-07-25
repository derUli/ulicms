<?php

class Model
{

    protected $id = null;

    public function __construct($id = null)
    {
        if (! is_null($id)) {
            $this->loadByID($id);
        }
    }

    public function loadByID($id)
    {
        throw new NotImplementedException("load not implemented");
    }

    public function save()
    {
        if (is_null($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    protected function fillVars($query = null)
    {
        throw new NotImplementedException("fillVars not implemented");
    }

    protected function insert()
    {
        throw new NotImplementedException("insert not implemented");
    }

    protected function update()
    {
        throw new NotImplementedException("update not implemented");
    }

    public function delete()
    {
        throw new NotImplementedException("delete not implemented");
    }

    public function setID($id)
    {
        $this->id = is_numeric($id) ? intval($id) : null;
    }

    public function getID()
    {
        return $this->id;
    }

    public function bindValues($values = array())
    {
        foreach ($values as $key => $value) {
            $camelCaseVar = ModuleHelper::underscoreToCamel($key);
            $method = "set" . ucfirst($camelCaseVar);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else if (isset($this->$value)) {
                $this->value = $value;
            } else if (isset($this->$camelCaseVar)) {
                $this->$camelCaseVar = $value;
            }
        }
    }

    public static function checkValueType($value, $type, $required = false)
    {
        if ($required and $value === null) {
            throw new InvalidArgumentException("Required field not filled");
        }
        $isXyzFunction = "is_" . $type;
        if (function_exists($isXyzFunction) and ! $isXyzFunction($value)) {
            throw new InvalidArgumentException("\"{$value}\" is not of type {$type}.");
        } else if (class_exists($type) and $value instanceof $type) {
            throw new InvalidArgumentException("\"{$value}\" is not of type {$type}.");
        }
    }
}