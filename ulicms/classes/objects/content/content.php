<?php
abstract class Content {
	abstract protected function loadByID($id);
	abstract protected function loadBySystemnameAndLanguage($name, $language);
	abstract protected function update();
	abstract protected function create();
	abstract protected function save();
	public function getShowHeadline() {
		$retval = true;
		$query = Database::query ( "SELECT `show_headline` FROM content where id =" . intval ( $this->id ) );
		if ($query) {
			$data = Database::fetch ( $query );
			$retval = boolval ( $data->show_headline );
		}
		return $retval;
	}
}