<?php
class Video_Page extends Page {
	public $video = null;
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->video = $result->audio;
	}
	public function save() {
		$retval = null;
		if ($this->id === null) {
			$retval = $this->create ();
			$this->update ();
		} else {
			$retval = $this->update ();
		}
		return $retval;
	}
	public function update() {
		$result = null;
		if ($this->id === null) {
			return $this->create ();
		}
		parent::update ();
		$sql = "update {prefix}content set video = ? where id = ?";
		$args = array (
				$this->video,
				$this->id 
		);
		
		$result = Database::pQuery ( $sql, $args, true );
		return $result;
	}
}