<?php
class Contact extends Model {
	private $name;
	private $firstname;
	private $phone;
	private $email;
	private $public = true;
	public function loadByID($id) {
		$sql = "select * from `{prefix}contact_book` where id = ?";
		$args = array (
				intval ( $id ) 
		);
		$query = Database::pQuery ( $sql, $args, true );
		$this->fillVars ( $query );
	}
	protected function fillVars($query = null) {
		if (is_null ( $query )) {
			$this->id = null;
			$this->name = null;
			$this->firstname = null;
			$this->phone = null;
			$this->email = null;
			$this->public = 1;
		} else {
			$data = Database::fetchSingle ( $query );
			$this->id = intval ( $data->id );
			$this->name = ! is_null ( $data->name ) ? strval ( $data->name ) : null;
			$this->firstname = ! is_null ( $data->firstname ) ? strval ( $data->firstname ) : null;
			$this->phone = ! is_null ( $data->phone ) ? strval ( $data->phone ) : null;
			$this->email = ! is_null ( $data->firstname ) ? strval ( $data->firstname ) : null;
			$this->public = ! is_null ( $data->public ) ? boolval ( $data->public ) : 1;
		}
	}
	public function insert() {
		$sql = "insert into `{prefix}contact_book` 
                (name, firstname, phone, email) values
                (?, ?, ?, ?)";
		$args = array (
				$this->getName (),
				$this->getFirstname (),
				$this->getPhone (),
				$this->getEmail () 
		);
		Database::pQuery ( $sql, $args, true );
		$this->setID ( Database::getLastInsertID () );
	}
	public function update() {
		$sql = "update `{prefix}contact_book` set name=?, 
				firstname=?, phone=?, email=? where id=?";
		$args = array (
				$this->getName (),
				$this->getFirstname (),
				$this->getPhone (),
				$this->getEmail (),
				$this->getID () 
		);
		Database::pQuery ( $sql, $args, true );
	}
	public static function search($subject) {
		$result = array ();
		$subject = strval ( $subject );
		$sql = "SELECT id, MATCH (name, firstname, phone, email) AGAINST (?) AS relevance
		FROM `{prefix}contact_book`
		WHERE MATCH (name, firstname, phone, email) AGAINST (?) and public = ?
		ORDER BY relevance DESC";
		$args = array (
				$subject,
				$subject,
				true 
		);
		
		$query = Database::pQuery ( $sql, $args, true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$result [] = new Contact ( $row->id );
		}
		return $result;
	}
	public static function getAll($order = "id") {
		$result = array ();
		$query = Database::query ( "select id from `{prefix}contact_book` order by $order" );
		while ( $row = Database::fetchObject ( $query ) ) {
			$result [] = new Contact ( $row->id );
		}
		return $result;
	}
	public function getName() {
		return $this->name;
	}
	public function getFirstname() {
		return $this->firstname;
	}
	public function getPhone() {
		return $this->phone;
	}
	public function getEmail() {
		return $this->email;
	}
	public function isPublic() {
		return $this->public;
	}
	public function delete() {
		Database::pQuery ( "delete from `{prefix}contact_book` where id = ? ", array (
				$this->id 
		), true );
		$this->fillVars ( null );
	}
	public function setName($val) {
		$this->name = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setFirstname($val) {
		$this->firstname = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setPhone($val) {
		$this->phone = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setEmail($val) {
		$this->email = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setPublic($val) {
		$this->public = boolval ( $val );
	}
}