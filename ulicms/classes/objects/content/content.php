<?php
abstract class Content extends Model {
	abstract protected function loadBySystemnameAndLanguage($name, $language);
	public function getShowHeadline() {
		$retval = true;
		$query = Database::query ( "SELECT `show_headline` FROM " . tbname ( "content" ) . " where id =" . intval ( $this->id ) );
		if ($query) {
			$data = Database::fetchObject ( $query );
			$retval = boolval ( $data->show_headline );
		}
		return $retval;
	}
}