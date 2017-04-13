<?php
class Audio_Page extends Page {
	public $audio = null;
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->audio = $result->audio;
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
		$sql = "update {prefix}content set audio = ? where id = ?";
		$args = array (
				$this->audio,
				$this->id 
		);
		
		$result = Database::pQuery ( $sql, $args, true );
		return $result;
	}
}