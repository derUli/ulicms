<?php
class Audio extends Model {
	private $name = null;
	private $mp3_file = null;
	private $ogg_file = null;
	private $category_id = null;
	private $created;
	private $updated;
	public function __construct($id = null) {
		if (! is_null ( $id )) {
			parent::__construct ( $id );
		} else {
			$this->created = time ();
			$this->updated = time ();
		}
	}
	public function loadById($id) {
		$query = Database::pQuery ( "select * from `{prefix}audio` where id = ?", array (
				intval ( $id ) 
		), true );
		if (! Database::any ( $query )) {
			$query = null;
		}
		$this->fillVars ( $query );
	}
	protected function fillVars($query = null) {
		if ($query) {
			$result = Database::fetchSingle ( $query );
			$this->setID ( $result->id );
			$this->setName ( $result->name );
			$this->setMP3File ( $result->mp3_file );
			$this->setOGGFile ( $result->ogg_file );
			$this->setCategoryId ( $result->category_id );
			$this->created = $result->created;
			$this->updated = $result->updated;
		} else {
			$this->setID ( null );
			$this->setName ( null );
			$this->setMP3File ( null );
			$this->setOGGFile ( null );
			$this->setCategoryId ( null );
			$this->created = null;
			$this->updated = null;
		}
	}
	public function getName() {
		return $this->name;
	}
	public function getMP3File() {
		return $this->mp3_file;
	}
	public function getOggFile() {
		return $this->ogg_file;
	}
	public function getCategoryId() {
		return $this->category_id;
	}
	public function getCreated() {
		return $this->created;
	}
	public function getUpdated() {
		return $this->updated;
	}
	public function setName($val) {
		$this->name = StringHelper::isNotNullOrWhitespace ( $val ) ? strval ( $val ) : null;
	}
	public function setMP3File($val) {
		$this->mp3_file = StringHelper::isNotNullOrWhitespace ( $val ) ? strval ( $val ) : null;
	}
	public function setOGGFile($val) {
		$this->ogg_file = StringHelper::isNotNullOrWhitespace ( $val ) ? strval ( $val ) : null;
	}
	public function setCategoryId($val) {
		$this->category_id = is_numeric ( $val ) ? intval ( $val ) : null;
	}
}