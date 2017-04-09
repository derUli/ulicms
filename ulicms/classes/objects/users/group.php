<?php
class Group {
	private $id = null;
	private $name = "";
	private $permissions = array ();
	public function __construct($id = null) {
		if (! is_null ( $id )) {
			$this->loadById ( $id );
		}
	}
	public function loadById($id) {
		$sql = "select * from `{prefix}groups` where id = ?";
		$args = array (
				intval ( $id ) 
		);
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::any ( $args )) {
			$result = Database::fetchObject ( $query );
			$this->id = $result->id;
			$this->name = $result->name;
			$this->permissions = json_decode ( $result->permissions, true );
			$acl = new ACL ();
			$allPermissions = $acl->getDefaultACL ();
			foreach ( $allPermissions as $name => $value ) {
				if (! isset ( $this->permissions [$name] )) {
					$this->addPermission ( $name, $value );
				}
			}
		}
	}
	public function save() {
		if ($this->id) {
			$this->update ();
		} else {
			$this->insert ();
		}
	}
	protected function insert() {
		throw new NotImplementedException ();
	}
	protected function update() {
		throw new NotImplementedException ();
	}
	public function delete() {
		if (is_null ( $this->id )) {
			return false;
		}
		$sql = "delete from `{prefix}groups` where id = ?";
		$args = array (
				$this->id 
		);
		$query = Database::pQuery ( $sql, $args, true );
		if ($query) {
			$this->id = null;
		}
		return $query;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = ! is_null ( $id ) ? $id : null;
	}
	public function getName() {
		return $this->getName ();
	}
	public function setName($name) {
		$this->name = ! is_null ( $name ) ? strval ( $name ) : null;
	}
	public function getPermissions() {
		return $this->permissions;
	}
	public function setPermissions($permissions) {
		$this->permissions = $permissions;
	}
	public function addPermission($name, $value = false) {
		$this->permissions [$name] = $value;
	}
	public function hasPermission($name) {
		return (in_array ( $name, $this->permissions ) and $this->permissions [$name]);
	}
	public function removePermission($name) {
		if (isset ( $this->permissions [$name] )) {
			unset ( $this->permissions [$name] );
		}
	}
}