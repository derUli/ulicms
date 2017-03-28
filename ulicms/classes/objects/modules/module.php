<?php
class Module {
	private $name = null;
	private $version = null;
	private $enabled = 0;
	public function __construct($name = null) {
		if ($name) {
			$this->loadByName ( $name );
		}
	}
	public function loadByName($name) {
		$sql = "select * from {prefix}modules where name = ?";
		$args = array (
				$name 
		);
		$query = Database::pQuery ( $sql, $args, true );
		$dataset = Database::fetchSingle ( $query );
		if ($dataset) {
			$this->name = $dataset->name;
			$this->version = $dataset->version;
			$this->enabled = $dataset->enabled;
			return true;
		}
		return false;
	}
	public function save() {
		$sql = "select * from {prefix}modules where name = ?";
		$args = array (
				$name 
		);
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::any ( $query )) {
			$this->update ();
		} else {
			$this->insert ();
		}
	}
	private function insert() {
		$sql = "INSERT INTO {prefix}modules (name, version, enabled) values(?, ?, ?)";
		$args = array (
				$this->name,
				$this->version,
				$this->enabled 
		);
		return Database::pQuery ( $sql, $args, true );
	}
	private function update() {
		$sql = "update {prefix}modules set version = ?, enabled = ? where name = ?";
		$args = array (
				$this->version,
				$this->enabled,
				$this->name 
		);
		return Database::pQuery ( $sql, $args, true );
	}
	public function getVersion() {
		return $this->version;
	}
	public function getName() {
		return $this->name;
	}
	public function isEnabled() {
		return boolval ( $this->enabled );
	}
	public function enable() {
		$this->enabled = 1;
		$this->save ();
	}
	public function disable() {
		$this->enabled = 0;
		$this->save ();
	}
	public function toggleEnabled() {
		if ($this->isEnabled ()) {
			$this->disable ();
		} else {
			$this->enable ();
		}
	}
	public function setName($name) {
		$this->name = strval ( $name );
	}
	public function setVersion($version) {
		$this->version = strval ( $version );
	}
	public function delete() {
		$sql = "select * from {prefix}modules where name = ?";
		$args = array (
				$name 
		);
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::any ( $query )) {
			$sql = "delete from {prefix}modules where name = ?";
			$args = array (
					$this->name 
			);
			return Database::pQuery ( $sql, $args, true );
		}
		return false;
	}
}