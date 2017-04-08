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
	private $group_id = null;
	private $notify_on_login = false;
	private $html_editor = "ckeditor";
	private $require_password_change = false;
	private $admin = false;
	private $password_changed = null;
	private $locked = false;
	private $last_login = null;
	private $twitter = "";
	private $homepage = "";
	public function __construct($id = null) {
		if ($id) {
			$this->loadById ( $id );
		}
	}
	public function loadById($id) {
		$sql = "select * from {prefix}users where id = ?";
		$args = array (
				intval ( $id ) 
		);
		$query = Database::pQuery ( $sql, $args, true );
		$this->fillVars ( $query );
	}
	public function loadByUsername($name) {
		$sql = "select * from {prefix}users where username = ?";
		$args = array (
				strval ( $name ) 
		);
		$query = Database::pQuery ( $sql, $args, true );
		$this->fillVars ( $query );
	}
	public function save() {
		if ($this->id) {
			$this->update ();
		} else {
			$this->insert ();
		}
	}
	public function fillVars($query) {
		if (Database::any ( $query )) {
			$result = Database::fetchAssoc ( $query );
			foreach ( $result as $key => $value ) {
				if (isset ( $this->$key ) || property_exists ( $this, $key )) {
					$this->$key = $value;
				}
			}
		}
	}
	protected function insert() {
		$sql = "insert into {prefix}users (username, lastname, firstname, email, password,
				old_encryption, skype_id, about_me, group_id, notify_on_login, html_editor, 
				require_password_change, admin, password_changed, locked, last_login, 
				twitter, homepage) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$args = array (
				$this->username,
				$this->lastname,
				$this->firstname,
				$this->email,
				$this->password,
				$this->old_encryption,
				$this->skype_id,
				$this->about_me,
				$this->group_id,
				$this->notify_on_login,
				$this->html_editor,
				$this->require_password_change,
				$this->admin,
				$this->password_changed,
				$this->locked,
				$this->last_login,
				$this->twitter,
				$this->homepage 
		);
		$result = Database::pQuery ( $sql, $args, true ) or die ( Database::getError () );
		if ($result) {
			$this->id = Database::getLastInsertID ();
		}
	}
	protected function update() {
		$sql = "update {prefix}users set username = ?, lastname = ?, firstname = ?, email = ?, password = ?,
				old_encryption = ?, skype_id = ?, about_me = ?, group_id = ?, notify_on_login = ?, html_editor = ?,
				require_password_change = ?, admin = ?, password_changed = ?, locked = ?, last_login = ?,
				twitter = ?, homepage = ? where id = ?";
		$args = array (
				$this->username,
				$this->lastname,
				$this->firstname,
				$this->email,
				$this->password,
				$this->old_encryption,
				$this->skype_id,
				$this->about_me,
				$this->group_id,
				$this->notify_on_login,
				$this->html_editor,
				$this->require_password_change,
				$this->admin,
				$this->password_changed,
				$this->locked,
				$this->last_login,
				$this->twitter,
				$this->homepage,
				$this->id 
		);
		$result = Database::pQuery ( $sql, $args, true ) or die ( Database::getError () );
		if ($result) {
			$this->id = Database::getLastInsertID ();
		}
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
		$this->email = ! is_null ( $email ) ? strval ( $email ) : null;
	}
	public function delete() {
		if (is_null ( $this->id )) {
			return false;
		}
		$sql = "delete from {prefix}users where id = ?";
		$args = array (
				$this->id 
		);
		$result = Database::pQuery ( $sql, $args, true );
		if ($result) {
			$this->id = null;
		}
		return $result;
	}
	public function getPassword() {
		return $this->password;
	}
	public function setPassword($password) {
		$this->password = Encryption::hashPassword ( $password );
		$this->old_encryption = false;
		$this->password_changed = date ( "Y-m-d H:i:s" );
	}
	public function getPasswordChanged() {
		return $this->password_changed;
	}
	public function resetPassword() {
		throw new NotImplementedException ();
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
	public function getLastAction() {
		$result = 0;
		if (! is_null ( $this->id )) {
			
			$sql = "select last_action from {prefix}users where id = ?";
			$args = array (
					$this->id 
			);
			$query = Database::pQuery ( $sql, $args, true );
			if (Database::any ( $query )) {
				$data = Database::fetchObject ( $query );
				$result = $data->last_action;
			}
		}
		return $result;
	}
	public function setLastAction($time) {
		if (is_null ( $this->id )) {
			return;
		}
		$time = intval ( $time );
		$sql = "update {prefix}users set last_action = ? where id = ?";
		$args = array (
				$time,
				$this->id 
		);
		Database::pQuery ( $sql, $args, true );
	}
	public function getGroupId() {
		return $this->group_id;
	}
	public function setGroupId($gid) {
		$this->group_id = ! is_null ( $gid ) ? $gid : null;
	}
	public function getNotifyOnLogin() {
		return boolval ( $this->notify_on_login );
	}
	public function setNotifyOnLogin($val) {
		$this->notify_on_login = boolval ( $val );
	}
	public function getHTMLEditor() {
		return $this->html_editor;
	}
	public function setHTMLEditor($editor) {
		$allowedEditors = array (
				"ckeditor",
				"codemirror" 
		);
		if (! in_array ( $editor, $allowedEditors )) {
			$editor = "ckeditor";
		}
		$this->html_editor = $editor;
	}
	public function getRequirePasswordChange() {
		return $this->require_password_change;
	}
	public function setRequirePasswordChange($val) {
		$this->require_password_change = boolval ( $val );
	}
	public function getAdmin() {
		return $this->admin;
	}
	public function setAdmin($val) {
		$this->admin = boolval ( $val );
	}
	public function getLocked() {
		return $this->locked;
	}
	public function setLocked($val) {
		$this->locked = boolval ( $val );
	}
	public function getLastLogin() {
		return $this->last_login;
	}
	public function setLastLogin($val) {
		$this->last_login = ! is_null ( $val ) ? intval ( $val ) : null;
	}
	public function getFailedLogins() {
		$result = 0;
		if (! is_null ( $this->id )) {
			
			$sql = "select failed_logins from {prefix}users where id = ?";
			$args = array (
					$this->id 
			);
			$query = Database::pQuery ( $sql, $args, true );
			if (Database::any ( $query )) {
				$data = Database::fetchObject ( $query );
				$result = $data->failed_logins;
			}
		}
		return $result;
	}
	public function increaseFailedLogins() {
		if (is_null ( $this->id )) {
			return;
		}
		$time = intval ( $time );
		$sql = "update {prefix}users set failed_logins = failed_logins + 1 where id = ?";
		$args = array (
				$this->id 
		);
		Database::pQuery ( $sql, $args, true );
	}
	public function resetFailedLogins() {
		if (is_null ( $this->id )) {
			return;
		}
		$time = intval ( $time );
		$sql = "update {prefix}users set failed_logins = ? where id = ?";
		$args = array (
				0,
				$this->id 
		);
		Database::pQuery ( $sql, $args, true );
	}
	public function setFailedLogins($amount) {
		if (is_null ( $this->id )) {
			return;
		}
		$time = intval ( $time );
		$sql = "update {prefix}users set failed_logins = ? where id = ?";
		$args = array (
				$amount,
				$this->id 
		);
		Database::pQuery ( $sql, $args, true );
	}
	public function getTwitter() {
		return $this->twitter;
	}
	public function setTwitter($val) {
		$this->twitter = strval ( $val );
	}
	public function getHomepage() {
		return $this->homepage;
	}
	public function setHomepage($val) {
		$this->homepage = strval ( $val );
	}
}