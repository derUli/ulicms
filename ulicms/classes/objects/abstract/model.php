<?php
abstract class Model {
	protected $id = null;
	public function __construct($id = null) {
		if (! is_null ( $id )) {
			$this->loadByID ( $id );
		}
	}
	public function loadByID($id) {
		throw new NotImplementedException ( "load not implemented" );
	}
	public function save() {
		if (is_null ( $this->id )) {
			$this->insert ();
		} else {
			$this->update ();
		}
	}
	protected function fillVars() {
		throw new NotImplementedException ( "fillVars not implemented" );
	}
	protected function insert() {
		throw new NotImplementedException ( "insert not implemented" );
	}
	protected function update() {
		throw new NotImplementedException ( "update not implemented" );
	}
	public function delete() {
		throw new NotImplementedException ( "delete not implemented" );
	}
	public function setID($id) {
		$this->id = is_int ( $id ) ? intval ( $id ) : null;
	}
	public function getID() {
		return $id;
	}
}