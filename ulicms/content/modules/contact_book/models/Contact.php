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
	public static function search($subject) {
		throw new NotImplementedException ();
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
	// @TODO Setter implementieren
}