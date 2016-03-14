<?php
class ContentFactory {
	public static function getByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT `type` FROM `" . tbname ( "content" ) . "` where id = " . $id );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			return self::getContentObjectByID ( $result );
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
			return self::getContentObjectByID ( $result );
		} else {
			throw new Exception ( "No page with this combination of $name and $language" );
		}
	}
	private static function getContentObjectByID($row) {
		$retval = null;
		if ($row->type == "page") {
			$retval = new Page ();
			$retval->loadByID ( $row->id );
		}
		return $retval;
	}
	public static function getAll($order = "id") {
		$result = array ();
		$sql = "SELECT id, `type` FROM " . tbname ( "content" ) . " ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$result [] = self::getContentObjectByID ( $row );
		}
		return $result;
	}
	public static function getByLanguage($language, $order = "id") {
		$language = DB::escapeValue ( $language );
		$result = array ();
		$sql = "SELECT id, `type` FROM " . tbname ( "content" ) . " where `language` = '$language' ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$result [] = self::getContentObjectByID ( $row );
		}
		return $result;
	}
	
	// @TODO: Funktionen für Abfrage von mehreren Datensätzen
	// z.B.
	// getByMenu()
	// getByMenuAndLanguage()
	// usw.
}
