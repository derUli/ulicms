<?php
class ContentFactory {
	public static function getByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT `type` FROM `" . tbname ( "content" ) . "` where id = " . $id );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			if ($result->type == "page") {
				$page = new Page ();
				return $page->loadByID ( $id );
			}
		} else {
			throw new Exception ( "No page with id $id" );
		}
	}
}