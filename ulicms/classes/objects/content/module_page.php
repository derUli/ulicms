<?php
class Module_Page extends Page {
	public $module = null;
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->module = $result->module;
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
		$sql = "update {prefix}content set module = ?, text_position = ? where id = ?";
		$args = array (
				$this->module,
				$this->text_position,
				$this->id 
		);
		
		$result = Database::pQuery ( $sql, $args, true );
		return $result;
	}
	public function containsModule($module = false) {
		$retval = false;
		if (parent::containsModule ( $module )) {
			$retval = true;
		}
		if ($this->module !== null and ! empty ( $this->module )) {
			if (($module and $this->module == $module) or ! $module) {
				$retval = true;
			}
		}
		return $retval;
	}
}