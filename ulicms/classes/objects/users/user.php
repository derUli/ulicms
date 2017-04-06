<?php
class User {
	private $id = null;
	private $username = null;
	private $lastname = "";
	private $firstname = "";
	private $email = "";
	private $password = "";
	private $old_encryption = false;
	private $skype_id = "";
	private $about_me = "";
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
	public function getFirstname() {
		return $this->firstname;
	}
	public function setFirstname($firstname) {
		$this->firstname = ! is_null ( $firstname ) ? strval ( $firstname ) : null;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = ! is_null ( $email ) ? strval ( $firstname ) : null;
	}
	public function delete() {
		throw new NotImplementedException ();
	}
	public function getPassword() {
		return $this->password;
	}
	public function setPassword($password) {
		$this->password = securityHelper::hash_password ( $password );
		$this->old_encryption = true;
	}
	public function getOldEncryption() {
		return $this->old_encryption;
	}
	public function setOldEncryption($value) {
		$this->old_encryption = boolval ( $value );
	}
	public function getSkypeId() {
		return $this->skype_id;
	}
	public function setSkypeId($skype_id) {
		$this->skype_id = ! is_null ( $skype_id ) ? strval ( $skype_id ) : null;
	}
	public function getAboutMe() {
		return $this->about_me;
	}
	public function setAboutMe($text) {
		$this->about_me = ! is_null ( $text ) ? strval ( $text ) : null;
	}
}