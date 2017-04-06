<?php
class User {
	private $id = null;
	private $username = null;
	private $lastname = null;
	public function __construct($id = null) {
		if ($id) {
			$this->loadById ( $id );
		}
	}
	public function loadById($id) {
		throw new NotImplementedException ();
	}
	public function loadByUsername($name) {
		throw new NotImplementedException ();
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
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = ! is_null ( $id ) ? intval ( $id ) : null;
	}
	public function getUsername() {
		return $this->username;
	}
	public function setUsername($username) {
		$this->username = ! is_null ( $username ) ? strval ( $username ) : null;
	}
	public function getLastname() {
		return $this->lastname;
	}
	public function setLastname($lastname) {
		$this->lastname = ! is_null ( $lastname ) ? strval ( $lastname ) : null;
	}
	public function delete() {
		throw new NotImplementedException ();
	}
}