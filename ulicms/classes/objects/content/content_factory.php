<?php
class ContentFactory {
	public static function getByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT `type` FROM `" . tbname ( "content" ) . "` where id = " . $id );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			if ($result->type == "page") {
				$page = new Page ();
				$page->loadByID ( $id );
				return $page;
			}
		} else {
			throw new Exception ( "No page with id $id" );
		}
	}
	public static function loadBySystemnameAndLanguage($name, $language) {
		$name = DB::escapeValue ( $name );
		$language = DB::escapeValue ( $language );
		$query = DB::query ( "SELECT id, `type` FROM `" . tbname ( "content" ) . "` where `systemname` = '$name' and `language` = '$language'" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			if ($result->type == "page") {
				$page = new Page ();
				$page->loadByID ( $result->$id );
				return $page;
			}
		} else {
			throw new Exception ( "No page with this combination of $name and $language" );
		}
	}
	public static function getAll($order = "id") {
		$result = array ();
		$sql = "SELECT id, `type` FROM " . tbname ( "content" ) . " ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			if ($result->type == "page") {
				$page = new Page ();
				$page->loadByID ( $row->id );
				$result [] = $page;
			}
		}
		return $result;
	}
	
	// @TODO: Funktionen für Abfrage von mehreren Datensätzen
	// z.B.
	// getByLanguage()
	// getByMenu()
	// getByMenuAndLanguage()
	// usw.
}
