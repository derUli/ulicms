<?php
class Banners {
	public static function getAll($order = "id") {
		$result = array ();
		$sql = "SELECT id FROM " . tbname ( "banner" ) . " ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$banner = new Banner ();
			$banner->loadByID ( $row->id );
			$result [] = $banner;
		}
		return $result;
	}
	public static function getByLanguage($language, $order = "language") {
		$language = DB::escapeValue ( $langauge );
		$result = array ();
		$sql = "SELECT id FROM " . tbname ( "banner" ) . " WHERE language = '$language' ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$banner = new Banner ();
			$banner->loadByID ( $row->id );
			$result [] = $banner;
		}
		return $result;
	}
	public static function getByCategory($category, $order = "id") {
		$category = intval ( $category );
		$result = array ();
		$sql = "SELECT id FROM " . tbname ( "banner" ) . " WHERE `category` = $category ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$banner = new Banner ();
			$banner->loadByID ( $row->id );
			$result [] = $banner;
		}
		return $result;
	}
	public static function getByType($type = "gif", $order = "language") {
		$type = DB::escapeValue ( $type );
		$result = array ();
		$sql = "SELECT id FROM " . tbname ( "banner" ) . " WHERE `type` = '$type' ORDER BY $order";
		$query = DB::query ( $sql );
		while ( $row = DB::fetchObject ( $query ) ) {
			$banner = new Banner ();
			$banner->loadByID ( $row->id );
			$result [] = $banner;
		}
		return $result;
	}
}