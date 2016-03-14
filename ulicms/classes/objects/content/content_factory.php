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
	public static function getAllByLanguage($language, $order = "id") {
		$language = DB::escapeValue ( $language );
		$result = array ();
		$sql = "SELECT id, `type` FROM " . tbname ( "content" ) . " where `language` = '$language' ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$result [] = self::getContentObjectByID ( $row );
		}
		return $result;
	}
	public static function getAllByMenu($menu, $order = "id") {
		$menu = DB::escapeValue ( $menu );
		$result = array ();
		$sql = "SELECT id, `type` FROM " . tbname ( "content" ) . " where `menu` = '$menu' ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$result [] = self::getContentObjectByID ( $row );
		}
		return $result;
	}
	public static function getAllByMenuAndLanguage($menu, $language, $order = "id") {
		$menu = DB::escapeValue ( $menu );
		$language = DB::escapeValue ( $language );
		$result = array ();
		$sql = "SELECT id, `type` FROM " . tbname ( "content" ) . " where `menu` = '$menu' and language = '$language' ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$result [] = self::getContentObjectByID ( $row );
		}
		return $result;
	}
	public static function filterByEnabled($elements, $enabled = 1) {
		$result = array ();
		foreach ( $elements as $element ) {
			if ($element->active == $enabled) {
				$result [] = $element;
			}
		}
		return $result;
	}
	public static function filterByCategory($elements, $category = 1) {
		$result = array ();
		foreach ( $elements as $element ) {
			if ($element->category == $category) {
				$result [] = $element;
			}
		}
		return $result;
	}
	public static function filterByAutor($elements, $autor = 1) {
		$result = array ();
		foreach ( $elements as $element ) {
			if ($element->autor == $autor) {
				$result [] = $element;
			}
		}
		return $result;
	}
	public static function filterByLastChangeBy($elements, $lastchangeby = 1) {
		$result = array ();
		foreach ( $elements as $element ) {
			if ($element->lastchangeby == $lastchangeby) {
				$result [] = $element;
			}
		}
		return $result;
	}
}
