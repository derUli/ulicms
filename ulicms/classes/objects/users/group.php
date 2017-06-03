<?php
class Group {
	private $id = null;
	private $name = "";
	private $permissions = array ();
	private $languages = array ();
	private $allowable_tags = null;
	public function __construct($id = null) {
		$acl = new ACL ();
		$this->permissions = $acl->getDefaultACLAsJSON ( false, true );
		if (! is_null ( $id )) {
			$this->loadById ( $id );
		}
	}
	public function getCurrentGroup() {
		if (isset ( $_SESSION ["group_id"] )) {
			$this->loadById ( $_SESSION ["group_id"] );
		}
	}
	public static function getAll() {
		$sql = "select id from `{prefix}groups` order by id";
		$query = Database::query ( $sql, true );
		$data = array ();
		while ( $row = Database::fetchobject ( $query ) ) {
			$data [] = new Group ( $row->id );
		}
		return $data;
	}
	public function loadById($id) {
		$sql = "select * from `{prefix}groups` where id = ?";
		$args = array (
				intval ( $id ) 
		);
		$query = Database::pQuery ( $sql, $args, true );
		if (Database::any ( $query )) {
			$result = Database::fetchObject ( $query );
			$this->id = $result->id;
			$this->name = $result->name;
			$this->permissions = json_decode ( $result->permissions, true );
			$this->allowable_tags = $result->allowable_tags;
			$acl = new ACL ();
			$allPermissions = $acl->getDefaultACLAsJSON ( false, true );
			foreach ( $allPermissions as $name => $value ) {
				if (! isset ( $this->permissions [$name] )) {
					$this->addPermission ( $name, $value );
				}
			}
		}
		$this->languages = array ();
		$sql = "select `language_id` from `{prefix}group_languages` where `group_id` = ?";
		$args = array (
				$this->getId () 
		);
		$query = Database::pQuery ( $sql, $args, true );
		while ( $row = Database::fetchobject ( $query ) ) {
			$lang = new Language ();
			$lang->loadById ( $row->language_id );
			if (! is_null ( $lang->getID () )) {
				$this->languages [] = $lang;
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
	protected Function saveLanguages() {
		$sql = "delete from `{prefix}group_languages` where `group_id` = ?";
		$args = array (
				$this->getId () 
		);
		Database::pQuery ( $sql, $args, true );
		foreach ( $this->languages as $lang ) {
			$sql = "insert into `{prefix}group_languages` (`group_id`,
 `language_id`) values(?, ?)";
			$args = array (
					$this->getId (),
					$lang->getID () 
			);
			Database::pQuery ( $sql, $args, true );
		}
	}
	protected function insert() {
		$sql = "insert into `{prefix}groups` (name, permissions, allowable_tags) values (?,?,?)";
		$args = array (
				$this->getName (),
				json_encode ( $this->getPermissions () ),
				$this->getAllowableTags () 
		);
		$query = Database::pQuery ( $sql, $args, true );
		if ($query) {
			$id = Database::getInsertID ();
			$this->id = $id;
			$this->saveLanguages ();
		}
	}
	protected function update() {
		$sql = "update `{prefix}groups` set name = ?, permissions = ?, allowable_tags = ? where id = ?";
		$args = array (
				$this->getName (),
				json_encode ( $this->getPermissions () ),
				$this->getAllowableTags (),
				$this->id 
		);
		$retval = Database::pQuery ( $sql, $args, true );
		$this->saveLanguages ();
		return $retval;
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
		return $this->name;
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
		return (faster_in_array ( $name, $this->permissions ) and $this->permissions [$name]);
	}
	public function removePermission($name) {
		if (isset ( $this->permissions [$name] )) {
			unset ( $this->permissions [$name] );
		}
	}
	public function getLanguages() {
		return $this->languages;
	}
	public function setLanguages($val) {
		$this->languages = $val;
	}
	public function getAllowableTags() {
		return $this->allowable_tags;
	}
	public function setAllowableTags($val) {
		$this->allowable_tags = Stringhelper::isNotNullOrWhitespace ( $val ) ? strval ( $val ) : null;
	}
}