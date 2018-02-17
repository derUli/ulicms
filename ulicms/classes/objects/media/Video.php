<?php
class Video extends Model {
	private $name = null;
	private $mp4_file = null;
	private $ogg_file = null;
	private $webm_file = null;
	private $category_id = null;
	private $category = null;
	private $created;
	private $updated;
	const VIDEO_DIR = "content/videos/";
	public function __construct($id = null) {
		if (! is_null ( $id )) {
			$this->loadById ( $id );
		} else {
			$this->created = time ();
			$this->updated = time ();
		}
	}
	public function loadById($id) {
		$query = Database::pQuery ( "select * from `{prefix}videos` where id = ?", array (
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
			$this->mp4_file = $result->mp4_file;
			$this->ogg_file = $result->ogg_file;
			$this->webm_file = $result->webm_file;
			$this->setCategoryId ( $result->category_id );
			$this->created = $result->created;
			$this->updated = $result->updated;
		} else {
			$this->setID ( null );
			$this->setName ( null );
			$this->mp4_file = null;
			$this->ogg_file = null;
			$this->webm_file = null;
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
				$this->mp4_file,
				$this->ogg_file,
				$this->webm_file,
				$this->category_id,
				$this->created,
				$this->updated 
		);
		$sql = "insert into `{prefix}videos` 
				(name, mp4_file, ogg_file, webm_file, category_id, created, updated)
				values (?, ?, ?, ?, ?, ?, ?)";
		Database::pQuery ( $sql, $args, true );
		$this->setID ( Database::getLastInsertID () );
	}
	protected function update() {
		$this->updated = time ();
		$args = array (
				$this->name,
				$this->mp4_file,
				$this->ogg_file,
				$this->webm_file,
				$this->category_id,
				$this->updated,
				$this->getID () 
		);
		$sql = "update `{prefix}videos` set
				name = ?, mp4_file = ?, ogg_file = ?, webm_file = ?, category_id = ?, updated = ?
				where id = ?";
		Database::pQuery ( $sql, $args, true );
	}
	public function getName() {
		return $this->name;
	}
	public function getMp4File() {
		return $this->mp4_file;
	}
	public function getOggFile() {
		return $this->ogg_file;
	}
	public function getWebmFile() {
		return $this->webm_file;
	}
	public function setMp4File($val) {
		$this->mp4_file = is_string ( $val ) ? $val : null;
	}
	public function setOggFile($val) {
		$this->ogg_file = is_string ( $val ) ? $val : null;
	}
	public function setWebmFile($val) {
		$this->webm_file = is_string ( $val ) ? $val : null;
	}
	public function getCategoryId() {
		return $this->category_id;
	}
	public function getCategory() {
		return $this->category;
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
	public function setCategoryId($val) {
		$this->category_id = is_numeric ( $val ) ? intval ( $val ) : null;
		$this->category = is_numeric ( $val ) ? new Category ( $val ) : null;
	}
	public function setCategory($val) {
		$this->category = ! is_null ( $val ) ? new Category ( $val ) : null;
		$this->category_id = $this->category->getID ();
	}
	public function delete($deletePhysical = true) {
		if ($this->getId ()) {
			if ($deletePhysical) {
				if ($this->getMp4File ()) {
					$file = Path::resolve ( "ULICMS_ROOT/content/videos/" . basename ( $this->getMP4File () ) );
					if (file_exists ( $file )) {
						@unlink ( $file );
					}
				}
				if ($this->getOggFile ()) {
					$file = Path::resolve ( "ULICMS_ROOT/content/videos/" . basename ( $this->getOggFile () ) );
					if (file_exists ( $file )) {
						@unlink ( $file );
					}
				}
				if ($this->getWebmFile ()) {
					$file = Path::resolve ( "ULICMS_ROOT/content/videos/" . basename ( $this->getWebmFile () ) );
					if (file_exists ( $file )) {
						@unlink ( $file );
					}
				}
			}
			Database::pQuery ( "delete from `{prefix}videos` where id = ?", array (
					$this->getID () 
			), true );
			$this->fillVars ( null );
		}
	}
	public function getHtml() {
		$html = '<video width="' . $this->width . '" height="' . $this->height . '" controls>';
		if (! empty ( $this->mp4_file )) {
			$html .= '<source src="' . self::VIDEO_DIR . htmlspecialchars ( $this->mp4_file ) . '" type="video/mp4">';
		}
		if (! empty ( $this->ogg_file )) {
			$html .= '<source src="' . self::VIDEO_DIR . htmlspecialchars ( $this->ogg_file ) . '" type="video/ogg">';
		}
		if (! empty ( $this->webm_file )) {
			$html .= '<source src="' . self::VIDEO_DIR . htmlspecialchars ( $this->webm_file ) . '" type="video/webm">';
		}
		$html .= get_translation ( "no_html5" );
		if (! empty ( $this->mp4_file ) or ! empty ( $this->ogg_file ) or ! empty ( $this->webm_file )) {
			$preferred = (! empty ( $this->mp4_file ) ? $this->mp4_file : (! empty ( $this->ogg_file ) ? $this->ogg_file : $this->webm_file));
			
			$html .= '<br/><a href="' . self::VIDEO_DIR . $preferred . '">' . get_translation ( "DOWNLOAD_VIDEO_INSTEAD" ) . '</a>';
		}
		$html .= "</video>";
		return $html;
	}
	public function html() {
		echo $this->getHtml ();
	}
}