<?php
class Language_Link extends Page {
	public $link_to_language = null;
	public $type = "link";
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
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->link_to_language = $result->link_to_language;
	}
	public function update() {
		$result = null;
		if ($this->id === null) {
			return $this->create ();
		}
		parent::update ();
		$sql = "update {prefix}content set link_to_language = ? where id = ?";
		$args = array (
				$this->link_to_language,
				$this->id 
		);
		
		$result = Database::pQuery ( $sql, $args, true );
		return $result;
	}
}