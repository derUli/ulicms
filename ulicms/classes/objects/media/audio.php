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
	public function setCategoryId($val) {
		$this->category_id = is_numeric ( $val ) ? intval ( $val ) : null;
	}
}