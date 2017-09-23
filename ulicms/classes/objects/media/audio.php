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
	protected function insert() {
		$this->created = time ();
		$this->updated = $this->created;
		$args = array (
				$this->name,
				$this->mp3_file,
				$this->ogg_file,
				$this->category_id,
				$this->created,
				$this->updated 
		);
		$sql = "insert into `{prefix}audio` 
				(name, mp3_file, ogg_file, category_id, created, updated)
				values (?, ?, ?, ?, ?, ?)";
		Database::pQuery ( $sql, $args, true );
		$this->setID ( Database::getLastInsertID () );
	}
	protected function update() {
		$this->updated = time ();
		$args = array (
				$this->name,
				$this->mp3_file,
				$this->ogg_file,
				$this->category_id,
				$this->updated,
				$this->getID () 
		);
		$sql = "update `{prefix}audio` set
				name = ?, mp3_file = ?, ogg_file = ?, category_id = ?, updated = ?
				where id = ?";
		Database::pQuery ( $sql, $args, true );
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
	public function delete($deletePhysical = true) {
		if ($this->get_ID ()) {
			if ($deletePhysical) {
				if ($this->getMP3File ()) {
					$file = Path::resolve ( "ULICMS_ROOT/content/audio/" . basename ( $this->getMP3File () ) );
					if (file_exists ( $file )) {
						@unlink ( $file );
					}
				}
				if ($this->getOggFile ()) {
					$file = Path::resolve ( "ULICMS_ROOT/content/audio/" . basename ( $this->getOggFile () ) );
					if (file_exists ( $file )) {
						@unlink ( $file );
					}
				}
			}
			Database::pQuery ( "delete from `{prefix}audio` where id = ?", array (
					$this->getID () 
			), true );
			$this->fillVars ( null );
		}
	}
}