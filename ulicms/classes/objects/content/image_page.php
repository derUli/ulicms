<?php
class Image_Page extends Page {
	public $image_url = null;
	public $text_position = "after";
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->image_url = $result->image_url;
		$this->text_position = $result->text_position;
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
		$sql = "update {prefix}content set image_url = ?, text_position = ? where id = ?";
		$args = array (
				$this->image_url,
				$this->text_position,
				$this->id 
		);
		
		$result = Database::pQuery ( $sql, $args, true );
		return $result;
	}
}