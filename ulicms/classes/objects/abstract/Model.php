<?php

use UliCMS\Exceptions\NotImplementedException;

// This is a base class for database models
// one model contains the definition of a database table and methods to load,
// create, update and delete and manipulate it's values
// you have to override all methods that are throwing NotImplementedException
// in your model classes

class Model {

	protected $id = null;

	// calls loadById if $id is not null
	public function __construct($id = null) {
		if (!is_null($id)) {
			$this->loadByID($id);
		}
	}

	// this method loads a dataset by id from $_GET
	public function loadByRequestId() {
		$id = Request::getVar("id");
		if (is_numeric($id)) {
			$this->loadByID(intval($id));
		}
	}

	// override this method to implement your sql select statement
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

	// $result must be a mysqli result or null
	// use this method to fill the data from database to
	// class variables
	protected function fillVars($result = null) {
		throw new NotImplementedException("fillVars not implemented");
	}

	// override this method to implement your sql insert statement
	protected function insert() {
		throw new NotImplementedException("insert not implemented");
	}

	// override this method to implement your sql update statement
	protected function update() {
		throw new NotImplementedException("update not implemented");
	}

	// override this method to implement your sql delete statement
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
	public static function checkValueType($value, $type, $required = false): bool {
		// if it's required and $value is null throw exception
		if ($required and $value === null) {
			throw new InvalidArgumentException("Required field not filled");
		}
		$isXyzFunction = "is_" . $type;
		// if it's null and not required it's ok
		if ($type === null) {
			return true;
		}
		if (function_exists($isXyzFunction)
				and ! var_is_type($value, $type, $required)) {
			throw new InvalidArgumentException("\"{$value}\" is not of type {$type}.");
		} else if (class_exists($type) and $value instanceof $type) {
			$dumpedValue = var_dump_str($value);
			throw new InvalidArgumentException("\"{$dumpedValue}\" is not of type {$type}.");
		}
		return true;
	}

	public static function getAllDatasets(
			$tableName,
			$modelClass,
			$orderBy = "id",
			$where = ""
	) {
		$datasets = [];
		$result = Database::selectAll($tableName, array(
					"id"
						), $where, [], true, $orderBy);
		while ($row = Database::fetchObject($result)) {
			$datasets[] = new $modelClass($row->id);
		}
		return $datasets;
	}

	// returns true if the database exists in database
	// returns false if it weren't saved yet to database
	public function isPersistent(): bool {
		return $this->getID() >= 1;
	}

	// returns true if there are any unsaved changes to this dataset
	public function hasChanges() {
		$hasChanges = false;
		$className = get_class($this);
		$originalDataset = new $className($this->getID());

		$reflection = new ReflectionClass($this);

		$vars = $reflection->getProperties();
		foreach ($vars as $property) {
			$camelCaseVar = ModuleHelper::underscoreToCamel(
							$property->getName()
			);
			$method = "get" . ucfirst($camelCaseVar);

			if (method_exists($this, $method)
					and $this->$method() != $originalDataset->$method()) {
				$hasChanges = true;
			}
		}
		return $hasChanges;
	}

}
